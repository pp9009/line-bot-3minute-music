<?php
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/include.php');

$delete_music = new DeleteMusic();
$delete_music->deleteOlderMusic();

$spotify = new SpotifyApi();
$search_result = $spotify->execRandomQuerySearch('track', ['market' => 'JP']);

$save_track = new SaveMusic();
$save_track->invoke($search_result->tracks);

echo 'success!';
