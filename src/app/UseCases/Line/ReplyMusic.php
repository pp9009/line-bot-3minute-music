<?php

namespace App\UseCases\Line;

use Illuminate\Support\Facades\DB;
use LINE\Clients\MessagingApi\Model\TextMessage;

;
use App\Models\User;
use App\UseCases\Line\Share\ApiRequest;

class ReplyMusic
{
    // 1 minute ＝ 60000 msecond
    public const ONEMINUTE_TO_MSEC = 60000;

    // リクエストされた分数 +- ALLOWANCE_MSEC を許容する
    // 下記の値で3分がリクエストされた場合 +-5秒を許容するため、2分55秒~3分05秒の曲を許容する
    public const ALLOWANCE_MSEC = 5000;

    /**
     * ReplyMessageを送信する
     *
     * @param mixed $event
     * @return \Illuminate\Http\Client\Response
     */
    public function invoke($event)
    {
        User::where('id', $event->offsetGet("source")->getUserId())
            ->increment('used_count');

        $request_minutes = str_replace('分', '', $event->getMessage()->getText());
        $tracks = DB::table('tracks')
            ->select('external_url')
            ->where('isrc', 'like', 'jp%')
            ->whereBetween('duration_ms', [self::ONEMINUTE_TO_MSEC * $request_minutes - self::ALLOWANCE_MSEC, self::ONEMINUTE_TO_MSEC * $request_minutes + self::ALLOWANCE_MSEC])
            ->get();

        $api = new ApiRequest();
        if (count($tracks->all()) > 0) {
            return $api->replyMessage(
                $event->getReplyToken(),
                (new TextMessage(['text' => $tracks->random()->external_url]))->setType('text')
            );
        } else {
            return $api->replyMessage(
                $event->getReplyToken(),
                (new TextMessage(['text' => "該当の曲が見つかりませんでした"]))->setType('text')
            );
        }
    }
}
