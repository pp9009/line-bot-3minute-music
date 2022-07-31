<?php

namespace App\Helper;

use SpotifyWebAPI;

class SpotifyApi
{
    public function execURL($url)
    {
        $session = new SpotifyWebAPI\Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET')
        );
        $session->requestCredentialsToken();
        $access_token = $session->getAccessToken();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $result = json_decode($response, true);
        return $result;
    }

    public function execRandomQuerySearch($type, $option)
    {
        $session = new SpotifyWebAPI\Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET')
        );
        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $session->requestCredentialsToken();
        $access_token = $session->getAccessToken();
        $api->setAccessToken($access_token);

        $result = $api->search($this->getRandomSearchQuery(), $type, $option);
        return $result;
    }

    private function getRandomSearchQuery()
    {
        $str = 'abcdefghijklmnopqrstuvwxyz';
        $shuffled_str = substr(str_shuffle($str), 0, 1);

        $num = '01';
        $shuffled_num = substr(str_shuffle($num), 0, 1);

        $random_search = '';
        switch ($shuffled_num) {
            case 0:
                $random_search = $shuffled_str . '%';
                break;
            case 1:
                $random_search = '%' . $shuffled_str . '%';
                break;
        }
        return $random_search;
    }
}
