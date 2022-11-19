<?php

namespace App\UseCases\Line;

use Illuminate\Support\Facades\DB;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use App\Models\User;
use App\UseCases\Line\Share\ApiRequest;

class ReplyMusic
{
    // 1minute = 60000ms
    public const ONEMINUTE_TO_MS = 60000;

    /**
     * ReplyMessageを送信する
     *
     * @param mixed $event
     * @return \Illuminate\Http\Client\Response
     */
    public function invoke($event)
    {
        User::where('id', $event->getUserId())
            ->increment('used_count');

        $request_minutes = str_replace('分', '', $event->getText());
        $tracks = DB::table('tracks')
            ->select('uri')
            ->where('isrc', 'like', 'jp%')
            ->whereBetween('duration_ms', [self::ONEMINUTE_TO_MS * $request_minutes - 5000, self::ONEMINUTE_TO_MS * $request_minutes + 5000])
            ->get();

        $api = new ApiRequest();
        if (count($tracks->all()) > 0) {
            return $api->replyMessage(
                $event->getReplyToken(),
                new TextMessageBuilder($tracks->random()->uri)
            );
        } else {
            return $api->replyMessage(
                $event->getReplyToken(),
                new TextMessageBuilder("該当の曲が見つかりませんでした")
            );
        }
    }
}
