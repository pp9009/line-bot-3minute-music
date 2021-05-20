<?php
require(__DIR__ . './../vendor/autoload.php');
include(__DIR__ . '/include.php');

function main($db)
{
//    deleteData($db);
    $search_result = execSearchApi(getRandomSearch(), 'track', ['market' => 'JP']);
    //$search_result = execSearchApi('%punpee%', 'track', ['market' => 'JP']);
    saveSelectMinuteTrack($db, $search_result->tracks);

    $next_url = $search_result->tracks->next;
    while (!is_null($next_url)) {
        $next_url_result = execURL($next_url);
        $result_obj = json_decode(json_encode($next_url_result));
        saveSelectMinuteTrack($db, $result_obj->tracks);

        $next_url = $result_obj->tracks->next;
    }
    $db = null;
}

function execSearchApi($q, $type, $option)
{
    $session = new SpotifyWebAPI\Session(
        Conf::getValue('spotify', 'client.id'),
        Conf::getValue('spotify', 'client.secret')
    );
    $api = new SpotifyWebAPI\SpotifyWebAPI();
    $session->requestCredentialsToken();
    $accessToken = $session->getAccessToken();
    $api->setAccessToken($accessToken);

    $result = $api->search($q, $type, $option);
    return $result;
}

function execURL($url)
{
    $session = new SpotifyWebAPI\Session(
        Conf::getValue('spotify', 'client.id'),
        Conf::getValue('spotify', 'client.secret')
    );
    $session->requestCredentialsToken();
    $accessToken = $session->getAccessToken();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $result = json_decode($response, true);
    return $result;
}

function getRandomSearch()
{
    $str = 'abcdefghijklmnopqrstuvwxyz';
    $shuffled_str = substr(str_shuffle($str), 0, 1);

    $num = '01';
    $shuffled_num = substr(str_shuffle($num), 0, 1);

    $randomSearch = '';
    switch ($shuffled_num) {
        case 0:
            $randomSearch = $shuffled_str . '%';
            break;
        case 1:
            $randomSearch = '%' . $shuffled_str . '%';
            break;
    }
    return $randomSearch;
}

function saveSelectMinuteTrack($db, $tracks)
{
    $items = $tracks->items;
    if (is_null($items)) {
        return;
    }

    foreach ($items as $item) {
        $uri = '';
        $artists = '';
        $popularity = '';
        $duration_ms = '';
        $isrc = '';

        if (isBetweent($item->duration_ms)
            && isIsrcJp($item->external_ids->isrc)) {
            $uri = $item->external_urls->spotify;
            foreach ($item->artists as $artist) {
                $artists .= $artist->name . ',';
            }
            $popularity = $item->popularity;
            $duration_ms = $item->duration_ms;
            $isrc = $item->external_ids->isrc;
            dbUtill::insertMusicData($db, $uri, rtrim($artists, ','), $popularity, $duration_ms, $isrc);
        }
    }
}

function isBetweent($val)
{
    for ($minute = 1; $minute <= 8; $minute++) {
        $convert_ms = $minute * 60000;
        if (($convert_ms - 5000) <= $val && $val <= ($convert_ms + 5000)) {
            return true;
        }
    }

    return false;
}

function isIsrcJp($isrc)
{
    if ('JP' === substr($isrc, 0, 2)) {
        return true;
    }
    return false;
}

function deleteData($db)
{
    $thirty_days_ago = date("Y-m-d", strtotime("-30 day"));
    dbUtill::deleteMoreThan30dayAgoData($db, $thirty_days_ago);
}

main($db);




