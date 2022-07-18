<?php

include (__DIR__ . '/dotenv.php');
include (__DIR__ . '/Models/User.php');
include (__DIR__ . '/Models/Music.php');
include (__DIR__ . '/UseCases/User/Register.php');
include (__DIR__ . '/UseCases/User/Update.php');
include (__DIR__ . '/UseCases/Music/SpotifyApi.php');
include (__DIR__ . '/UseCases/Music/SaveMusic.php');
include (__DIR__ . '/UseCases/Music/DeleteMusic.php');