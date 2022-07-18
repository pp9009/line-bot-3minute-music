<?php

class Music
{

    public function __construct()
    {
        $dsn = 'mysql:host=mysql;dbname=' . Env::getValue('MYSQL_DATABASE_DB') . ';charset=utf8mb4';
        $this->db = new PDO($dsn, Env::getValue('MYSQL_USER'), Env::getValue('MYSQL_PASSWORD'));
    }

    public function saveTrack($uri, $artists, $popularity, $duration_ms, $isrc)
    {
        try {
            $sql = 'select * from music_data where uri = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$uri]);
            $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

            if (count($result) == 0) {
                $sql = 'insert into music_data(uri,artists,popularity,duration_ms,isrc,register_date) VALUES(?,?,?,?,?,NOW())';
                $stmt = $this->db->prepare($sql);
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

    public function deleteOlderMusic($target_date)
    {
        try {
            $sql = "delete from music_data where date_format( date_format( registdate, '%Y-%m-%d') >= ?, '%Y-%m-%d')";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$target_date]);
            return $result;
        } catch (PDOException $e) {
            error_log($e);
            return null;
        }
    }
}
