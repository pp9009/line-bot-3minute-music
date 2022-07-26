<?php

class User
{
    public function __construct()
    {
        $dsn = 'mysql:host=mysql;dbname=' . Env::getValue('MYSQL_DATABASE_DB') . ';charset=utf8mb4';
        $this->db = new PDO($dsn, Env::getValue('MYSQL_USER'), Env::getValue('MYSQL_PASSWORD'));
    }

    public function registerUser($userid)
    {
        try {
            $sql = "INSERT INTO users (userid, used_count, register_date, update_date)
                    VALUES (?, 0, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE update_date = VALUES(update_date)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userid]);
        } catch (PDOException $e) {
            error_log($e);
        }
    }

    public function updateUserCount($userid)
    {
        try {
            $sql = "update users set used_count = used_count + 1 where userid = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userid]);
        } catch (PDOException $e) {
            error_log($e);
        }
    }
}
