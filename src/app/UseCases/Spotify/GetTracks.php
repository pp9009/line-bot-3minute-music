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
        $search_items = $this->spotify_api->execRandomQuerySearch('track', ['market' => 'JP']);
        $this->saveTrack($search_items->tracks);
        $this->execNextUrl($search_items->tracks->next);
    }

    private function saveTrack($tracks)
    {
        foreach ($tracks->items as $item) {
            if ($this->validateTrack($item)) {
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
        }
    }

    private function execNextUrl($next_url)
    {
        while (!is_null($next_url)) {
            $search_items = json_decode(json_encode($this->spotify_api->execURL($next_url)));
            if (isset($search_items->error)) {
                return;
            }

            $this->saveTrack($search_items->tracks);
            $next_url = $search_items->tracks->next;
        }
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
