<?php

$dsn = 'mysql:host=mysql;dbname=' . Conf::getValue('db', 'db') . ';';
$db = new PDO($dsn, Conf::getValue('db', 'user'), Conf::getValue('db', 'password'));

$db->exec('create table if not exists music_data(
      id int not null auto_increment primary key,
      uri VARCHAR(255),
      artists VARCHAR(255),
      popularity int(11),
      duration_ms int(11),
      isrc VARCHAR(255),
      registdate datetime,
      updatedate datetime
      )');

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
                $sql = 'insert into music_data(uri,artists,popularity,duration_ms,isrc,registdate,updatedate) VALUES(?,?,?,?,?,NOW(),NOW())';
                $stmt = $db->prepare($sql);
                $stmt->execute([$uri, $artists, $popularity, $duration_ms, $isrc]);
            }
        } catch (PDOException $e) {
            var_dump($e);
        }
    }

    public static function getMusic($db, $text, $jp_flag = false)
    {
        try {
            if ($jp_flag) {
                $minute = substr($text, 0, 1);
                $sql = 'select * from music_data where duration_ms between (60000 * ? - 10000) and (60000 * ? + 10000) and isrc like ' . '"jp%"';
                $stmt = $db->prepare($sql);
                $stmt->execute([$minute, $minute]);
                $result = $stmt->fetchALL(PDO::FETCH_ASSOC);
            } else {
                $num = mt_rand(80, 100);
                $sql = 'select * from music_data where popularity >= ?';
                $stmt = $db->prepare($sql);
                $stmt->execute([$num]);
                $result = $stmt->fetchALL(PDO::FETCH_ASSOC);
            }
            $response = $result[array_rand($result)];
            return $response['uri'];
        } catch (PDOException $e) {
            var_dump($e);
            return null;
        }
    }

    public static function deleteMoreThan30dayAgoData($db, $target_day)
    {
        try {
            $sql = "delete from music_data where date_format( ?, '%Y-%m-%d') >= date_format( registdate, '%Y-%m-%d')";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([$target_day]);
            return $result;
        } catch (PDOException $e) {
            var_dump($e);
            return null;
        }
    }

}