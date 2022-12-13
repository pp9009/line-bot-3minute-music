<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Constant\HTTPHeader;
use App\UseCases\Line\QuickReply;
use App\UseCases\Line\ReplyMusic;
use App\UseCases\Line\Share\ApiRequest;
use Artisan;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $http_client = new CurlHTTPClient(env('LINE_CHANNEL_ACCESS_TOKEN'));
        $bot = new LINEBot($http_client, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);
        $events = $bot->parseEventRequest($request->getContent(), $request->header(mb_strtolower(HTTPHeader::LINE_SIGNATURE)));

        foreach ($events as $event) {
            if ($event->getText() === 'getMusic!!') {
                $usecase = new QuickReply();
                $usecase->invoke($event);
            } elseif (preg_match('/^[1-8]{1}分$/u', $event->getText())) {
                $usecase = new ReplyMusic();
                $usecase->invoke($event);
            } else {
                $api = new ApiRequest();
                $api->replyMessage(
                    $event->getReplyToken(),
                    new TextMessageBuilder("このBOTを使う時はメニューから\nget musicをタップしてね")
                );
            }

            // renderはcronが有料のためrequest毎にバッチを実行
            Artisan::call('command:getTracks');
        }
    }
}
