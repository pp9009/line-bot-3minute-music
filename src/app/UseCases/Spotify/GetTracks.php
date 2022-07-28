<?php

namespace App\Usecases\Spotify;

use App\Helper\SpotifyApi;
use App\Models\Music;

class GetTracks
{
    public const ONEMINUTE_TO_MSEC = 60000;

    public function __construct(SpotifyApi $spotify_api)
    {
        $this->spotify_api = $spotify_api;
    }

    public function invoke()
    {
        $search_result = $this->spotify_api->execRandomQuerySearch('track', ['market' => 'JP']);

        foreach ($search_result->tracks->items as $item) {
            if ($this->validateTrack($item)) {
                $this->saveTrack($item);
            }
        }

        $next_url = $search_result->tracks->next;
        $this->execNextUrl($next_url);
    }

    private function execNextUrl($next_url)
    {
        while (!is_null($next_url)) {
            $next_url_result = $this->spotify_api->execURL($next_url);
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

        Music::create([
            'uri' => $item->external_urls->spotify,
            'artists' => rtrim($artists, ','),
            'popularity' => $item->popularity,
            'duration_ms' => $item->duration_ms,
            'isrc' => $item->external_ids->isrc,
        ]);
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
                $val >= self::ONEMINUTE_TO_MSEC * $i - 5000
                && $val <= self::ONEMINUTE_TO_MSEC * $i + 5000
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
