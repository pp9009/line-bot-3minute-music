<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Constant\HTTPHeader;
use App\UseCases\Line\QuickReply;
use App\UseCases\Line\ReplyMusic;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $http_client = new CurlHTTPClient(env('LINE_CHANNEL_ACCESS_TOKEN'));
        $bot = new LINEBot($http_client, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);
        $events = $bot->parseEventRequest(file_get_contents('php://input'), $request->header(mb_strtolower(HTTPHeader::LINE_SIGNATURE)));

        foreach ($events as $event) {
            if ($event->getText() === 'getMusic!!') {
                $usecase = new QuickReply();
                $usecase->invoke($event);
            } elseif (preg_match('/^[1-8]{1}分$/u', $event->getText())) {
                $usecase = new ReplyMusic();
                $usecase->invoke($event);
            } else {
                $http_client = new CurlHTTPClient(env('LINE_CHANNEL_ACCESS_TOKEN'));
                $bot = new LINEBot($http_client, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);
                $bot->replyMessage(
                    $event->getReplyToken(),
                    new TextMessageBuilder("このBOTを使う時はメニューから\nget musicをタップしてね")
                );
            }
        }
    }
}
