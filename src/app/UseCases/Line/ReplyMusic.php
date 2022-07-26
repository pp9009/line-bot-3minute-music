<?php

namespace App\UseCases\Line;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Illuminate\Support\Facades\DB;

class ReplyMusic
{
    const ONEMIN_CONVERT_MS = 60000;

    public function invoke($event)
    {
        $minute = str_replace('分', '', $event->getText());
        $reply_text = DB::table('music_data')
            ->select('uri')
            ->where('isrc', 'like', 'jp%')
            ->whereBetween('duration_ms', [self::ONEMIN_CONVERT_MS * $minute - 5000, self::ONEMIN_CONVERT_MS * $minute + 5000])
            ->get()
            ->random();

        $http_client = new CurlHTTPClient(env('LINE_CHANNEL_ACCESS_TOKEN'));
        $bot = new LINEBot($http_client, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);
        $bot->replyMessage(
            $event->getReplyToken(),
            new TextMessageBuilder($reply_text)
        );
    }
}
