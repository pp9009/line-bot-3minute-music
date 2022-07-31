<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\SpotifyController;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['signature'])->post('/webhook', function (Request $request, WebhookController $webhook) {
    $http_client = new CurlHTTPClient(env('LINE_CHANNEL_ACCESS_TOKEN'));
    $bot = new LINEBot($http_client, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);
    $events = $bot->parseEventRequest(file_get_contents('php://input'), $request->header(mb_strtolower(HTTPHeader::LINE_SIGNATURE)));

    foreach ($events as $event) {
        if ($event->getText() === 'getMusic!!') {
            $webhook->startConversation($event);
        } elseif (preg_match('/^[1-8]{1}分$/u', $event->getText())) {
            $webhook->replyMusic($event);
        } else {
            $webhook->exception($event);
        }
    }
});

Route::get('/get-spotify-tracks', [SpotifyController::class,'getSpotifyTracks']);
