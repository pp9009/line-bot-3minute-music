<?php

$dsn = 'mysql:host=mysql;dbname=' . Env::getValue('db') . ';charset=utf8mb4';
$db = new \PDO($dsn, Env::getValue('user'), Env::getValue('password'));

class dbUtill
{
    public static function insertMusicData($db, $uri, $artists, $popularity, $duration_ms, $isrc)
    {
        try {
            $sql = 'select * from music_data where uri = ?';
            $stmt = $db->prepare($sql);
            $stmt->execute([$uri]);
            $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

            if (count($result) == 0) {
                $sql = 'insert into music_data(uri,artists,popularity,duration_ms,isrc,register_date) VALUES(?,?,?,?,?,NOW())';
                $stmt = $db->prepare($sql);
                $stmt->execute([$uri, $artists, $popularity, $duration_ms, $isrc]);
            }
        } catch (PDOException $e) {
            error_log($e);
        }
    }

    public static function registerUser($db, $userid)
    {
        try {
            $sql = "INSERT INTO users (userid, used_count, register_date, update_date)
                    VALUES (?, 0, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE update_date = VALUES(update_date)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$userid]);
        } catch (PDOException $e) {
            error_log($e);
        }
    }

    public static function getMusic($db, $text)
    {
        try {
            $minute = substr($text, 0, 1);
            $sql = 'select * from music_data where duration_ms between (60000 * ? - 5000) and (60000 * ? + 5000) and isrc like ' . '"jp%"';
            $stmt = $db->prepare($sql);
            $stmt->execute([$minute, $minute]);
            $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

            $response = $result[array_rand($result)];
            return $response['uri'];
        } catch (PDOException $e) {
            error_log($e);
            return null;
        }
    }

    public static function updateUserCount($db, $userid)
    {
        try {
            $sql = "update users set used_count = used_count + 1 where userid = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$userid]);
        } catch (PDOException $e) {
            error_log($e);
        }
    }

    public static function deleteMusicData($db)
    {
        try {
            for ($i = 1; $i <= 8; $i++) {
                $sql = 'select * from music_data where duration_ms between (60000 * ? - 5000) and (60000 * ? + 5000) and isrc like ' . '"jp%"';
                $stmt = $db->prepare($sql);
                $stmt->execute([$i, $i]);
                $music_data = $stmt->fetchALL(PDO::FETCH_ASSOC);

                if (count($music_data) > 10000) {
                    // 約n分のデータが1万件以上あった場合
                    $delete_limit = count($music_data) - 10000;
                    $sql = "delete from music_data order by registdate limit $delete_limit";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                }
            }
        } catch (PDOException $e) {
            error_log($e);
            return null;
        }
    }
}
