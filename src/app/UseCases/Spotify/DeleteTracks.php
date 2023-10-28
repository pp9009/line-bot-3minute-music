<?php

namespace App\UseCases\Spotify;

use Carbon\Carbon;
use App\Models\Tracks;

class DeleteTracks
{
    public function invoke()
    {
        $halfYearAgo = Carbon::now()->subMonths(6);
        Tracks::where('updated_at', '<', $halfYearAgo)->delete();
    }
}
