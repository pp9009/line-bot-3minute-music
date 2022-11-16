<?php

namespace App\UseCases\Line\Share;

use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder;
use Illuminate\Support\Facades\Http;

class ApiRequest
{
    public const REPLY_MESSAGE_ENDPOINT = LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply';

    /**
     * エンドポイントへリクエストを行う
     * https://developers.line.biz/ja/reference/messaging-api/#send-reply-message
     *
     * @param string $replyToken
     * @param MessageBuilder $messageBuilder
     * @return \Illuminate\Http\Client\Response
     */
    public function replyMessage($replyToken, MessageBuilder $messageBuilder)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN')
        ])->post(self::REPLY_MESSAGE_ENDPOINT, [
            'replyToken' => $replyToken,
            'messages' => $messageBuilder->buildMessage(),
        ]);
    }
}
