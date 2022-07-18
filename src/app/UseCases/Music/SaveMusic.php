<?php

class SaveMusic
{

    public function __construct()
    {
        $this->spotify = new SpotifyApi();
        $this->music = new Music();
    }

    public function invoke($music)
    {
        $items = $music->tracks->items;
        foreach ($items as $item) {
            if ($this->validateTrack($item)) {
                $saveData = $this->buildSaveData($item);
                $this->saveTrack($saveData);
            }
        }

        $next_url = $music->tracks->next;
        $this->execNextUrl($next_url);
    }

    private function execNextUrl($next_url)
    {
        while (!is_null($next_url)) {
            $next_url_result = $this->spotify->execURL($next_url);
            $result_obj = json_decode(json_encode($next_url_result));
            if (isset($result_obj->error)) {
                return;
            }

            $items = $result_obj->tracks->items;
            foreach ($items as $item) {
                if ($this->validateTrack($item)) {
                    $saveData = $this->buildSaveData($item);
                    $this->saveTrack($saveData);
                }
            }
            $next_url = $result_obj->tracks->next;
        }
    }

    private function buildSaveData($item)
    {
        $saveData = [];
        $artists = '';
        foreach ($item->artists as $artist) {
            $artists .= $artist->name . ',';
        }
        $saveData['artists'] = $artists;
        $saveData['uri'] = $item->external_urls->spotify;
        $saveData['popularity'] = $item->popularity;
        $saveData['duration_ms'] = $item->duration_ms;
        $saveData['isrc'] = $item->external_ids->isrc;
        return $saveData;
    }

    private function saveTrack($saveData)
    {
        $this->music->saveTrack(
            $saveData['uri'],
            rtrim($saveData['artists'], ','),
            $saveData['popularity'],
            $saveData['duration_ms'],
            $saveData['isrc']
        );
    }

    private function validateTrack($item)
    {
        if (
            $this->isIsrcJp($item->external_ids->isrc)
            && $this->validateTime($item->duration_ms)
        ) {
            return true;
        }
        return false;
    }

    private function validateTime($val)
    {
        // 1min = 60000ms
        $ms_list = [60000, 120000, 180000, 240000, 300000, 360000, 420000, 480000];
        foreach ($ms_list as $ms) {
            if (($ms - 5000) <= $val && $val <= ($ms + 5000)) {
                return true;
            }
        }
        return false;
    }

    private function isIsrcJp($isrc)
    {
        if (substr($isrc, 0, 2) === 'JP') {
            return true;
        }
        return false;
    }
}
