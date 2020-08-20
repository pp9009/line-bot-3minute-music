<?php

$dsn = "mysql:host=mysql;dbname=test_db;";
$db = new PDO($dsn, 'test', 'test');

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

    public static function getMusic($db, $jp_flag = false)
    {
        try {
            if ($jp_flag) {
                $sql = 'select * from music_data where isrc like '. '"jp%"';
                $stmt = $db->prepare($sql);
                $stmt->execute();
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
}