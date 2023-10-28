<?php

namespace App\UseCases\Line;

use App\Models\User;
use App\UseCases\Line\Share\ApiRequest;

class QuickReply
{
    /**
     * QuickReplyを送信する
     *
     * @param mixed $event
     * @return \Illuminate\Http\Client\Response
     */
    public function invoke($event)
    {
        User::upsert(
            ['id' => $event->offsetGet("source")->getUserId()],
            ['id'],
        );

        for ($minute = 1; $minute <= 8; $minute++) {
            $actions[] = (string)$minute . '分';
        }

        $api = new ApiRequest();
        return $api->quickReplyMessage(
            $event->getReplyToken(),
            '何分の曲にするか指定してね！',
            $actions
        );
    }
}
