<?php

namespace App\UseCases\Spotify;

use App\Helper\SpotifyApi;
use App\Models\Tracks;

class GetTracks
{
    // 1 minute ＝ 60000 msecond
    public const ONEMINUTE_TO_MSEC = 60000;
    // 60000 +- TOLERANCE_MSEC を許容する
    public const TOLERANCE_MSEC = 5000;

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
                Tracks::create([
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

    private function validateTime($msec)
    {
        for ($minute = 1; $minute <= 8; $minute++) {
            if (
                $msec >= $minute * self::ONEMINUTE_TO_MSEC - self::TOLERANCE_MSEC
                && $msec <= $minute * self::ONEMINUTE_TO_MSEC + self::TOLERANCE_MSEC
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
