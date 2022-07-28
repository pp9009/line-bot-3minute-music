<?php

namespace App\Http\Controllers;

use App\Usecases\Spotify\GetTracks;

class SpotifyController extends Controller
{
    public function getSpotifyTracks(GetTracks $usecase){
        $usecase->invoke();
        echo 'Successfully getting the spotify trucks';
    }
}
