<?php

namespace App\UseCases\Line\Share;

use Illuminate\Support\Facades\Http;

class ApiRequest
{
    public const REPLY_MESSAGE_ENDPOINT = 'https://api.line.me/v2/bot/message/reply';

    /**
     * エンドポイントへリクエストを行う
     * https://developers.line.biz/ja/reference/messaging-api/#send-reply-message
     *
     * @param string $replyToken
     * @param MessageBuilder $messageBuilder
     * @return \Illuminate\Http\Client\Response
     */
    public function replyMessage($replyToken, $message)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN')
        ])->post(self::REPLY_MESSAGE_ENDPOINT, [
            'replyToken' => $replyToken,
            'messages' => [$message],
        ]);
    }

    public function quickReplyMessage($replyToken, $text, $actions)
    {
        $messageObjects = [];

        foreach ($actions as $item) {
            $messageObject = [
                'type'  => 'action',
                'action' => [
                    'type' => 'message',
                    'label' => $item,
                    'text' => $item,
                ]
            ];
            $messageObjects[] = $messageObject;
        }

        $messages = [
            'type' => 'text',
            'text' => $text,
            'quickReply' => [
                'items' => $messageObjects,
            ],
        ];

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN')
        ])->post(self::REPLY_MESSAGE_ENDPOINT, [
            'replyToken' => $replyToken,
            'messages' => [$messages]

        ]);
    }
}
