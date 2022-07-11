<?php

class Music
{

    public function __construct()
    {
        $dsn = 'mysql:host=mysql;dbname=' . Env::getValue('db') . ';charset=utf8mb4';
        $this->db = new PDO($dsn, Env::getValue('user'), Env::getValue('password'));
    }

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

    public function getMusic($text)
    {
        try {
            $minute = substr($text, 0, 1);
            $sql = 'select * from music_data where duration_ms between (60000 * ? - 5000) and (60000 * ? + 5000) and isrc like ' . '"jp%"';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$minute, $minute]);
            $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

            $response = $result[array_rand($result)];
            return $response['uri'];
        } catch (PDOException $e) {
            error_log($e);
            return null;
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
