<?php

class SaveMusic
{
    public const MSEC = 60000;

    public function __construct()
    {
        $this->spotify = new SpotifyApi();
        $this->music = new Music();
    }

    public function invoke($tracks)
    {
        $items = $tracks->items;
        foreach ($items as $item) {
            if ($this->validateTrack($item)) {
                $this->saveTrack($item);
            }
        }

        $next_url = $tracks->next;
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
                    $this->saveTrack($item);
                }
            }
            $next_url = $result_obj->tracks->next;
        }
    }

    private function saveTrack($item)
    {
        $artists = '';
        foreach ($item->artists as $artist) {
            $artists .= $artist->name . ',';
        }

        $this->music->saveTrack(
            $item->external_urls->spotify,
            rtrim($artists, ','),
            $item->popularity,
            $item->duration_ms,
            $item->external_ids->isrc,
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
        for ($i = 1; $i <= 8; $i++) {
            if (
                $val >= self::MSEC * $i - 5000
                && $val <= self::MSEC * $i + 5000
            ) {
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
