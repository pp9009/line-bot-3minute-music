<?php

class DeleteMusic
{
    public function __construct()
    {
        $this->music = new Music();
    }

    public function deleteOlderMusic()
    {
        $target_date = date("Y-m-d", strtotime('-6 month'));
        $this->music->deleteOlderMusic($target_date);
    }
}
