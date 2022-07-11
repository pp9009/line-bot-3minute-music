<?php

include(__DIR__ . '../../db_operation.php');

class Update
{

    public function __construct()
    {
        $this->db = new dbUtill();
    }

    public function invoke($event)
    {
        $this->db->updateUserCount($event->getUserId());
        $text = $this->db->getMusic($event->getText());
        return $text;
    }
}
