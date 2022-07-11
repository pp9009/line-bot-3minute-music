<?php

include(__DIR__ . '../../Models/User.php');
include(__DIR__ . '../../Models/Music.php');

class Update
{

    public function __construct()
    {
        $this->user = new User();
        $this->music = new Music();
    }

    public function updateUser($event)
    {
        $this->user->updateUserCount($event->getUserId());
    }

    public function getMusic($event){
        $reply_text = $this->music->getMusic($event->getText());
        return $reply_text;
    }
}
