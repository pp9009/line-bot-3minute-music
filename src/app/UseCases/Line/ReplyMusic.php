<?php

namespace App\UseCases\Line;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ReplyMusic
{
    public const ONEMINUTE_CONVERT_TO_MSEC = 60000;

    public function invoke($event)
    {
        User::where('userid', $event->getUserId())
            ->increment('used_count');

        $minute = str_replace('分', '', $event->getText());
        $reply_text = DB::table('music_data')
            ->select('uri')
            ->where('isrc', 'like', 'jp%')
            ->whereBetween('duration_ms', [self::ONEMINUTE_CONVERT_TO_MSEC * $minute - 5000, self::ONEMINUTE_CONVERT_TO_MSEC * $minute + 5000])
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
