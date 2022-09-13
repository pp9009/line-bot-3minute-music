<?php

namespace App\UseCases\Line;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ReplyMusic
{
    public const ONEMINUTE_TO_MSEC = 60000;

    public function __construct()
    {
        $http_client = new CurlHTTPClient(env('LINE_CHANNEL_ACCESS_TOKEN'));
        $this->line_bot = new LINEBot($http_client, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);
    }

    public function invoke($event)
    {
        User::where('userid', $event->getUserId())
            ->increment('used_count');

        $minute = str_replace('分', '', $event->getText());
        $tracks = DB::table('tracks')
            ->select('uri')
            ->where('isrc', 'like', 'jp%')
            ->whereBetween('duration_ms', [self::ONEMINUTE_TO_MSEC * $minute - 5000, self::ONEMINUTE_TO_MSEC * $minute + 5000])
            ->get();

        if (count($tracks->all()) > 0) {
            $this->line_bot->replyMessage(
                $event->getReplyToken(),
                new TextMessageBuilder($tracks->random()->uri)
            );
        } else {
            $this->line_bot->replyMessage(
                $event->getReplyToken(),
                new TextMessageBuilder("該当の曲が見つかりませんでした")
            );
        }
    }
}
