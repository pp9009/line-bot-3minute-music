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

    public function invoke($event)
    {
        $this->user->updateUserCount($event->getUserId());
        $reply_text = $this->music->getMusic($event->getText());
        return $reply_text;
    }
}
