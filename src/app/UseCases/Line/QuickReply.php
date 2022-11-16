<?php

namespace App\UseCases\Line;

use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use App\Models\User;
use Illuminate\Support\Facades\Http;

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
            ['userid' => $event->getUserId()],
            ['userid'],
        );

        for ($i = 1; $i <= 8; $i++) {
            $button_list[] = new QuickReplyButtonBuilder(new MessageTemplateActionBuilder($i . '分', $i . '分'));
        }
        return $this->replyMessage(
            $event->getReplyToken(),
            new TextMessageBuilder('何分の曲にするか指定してね！', new QuickReplyMessageBuilder($button_list))
        );
    }

    /**
     * エンドポイントへリクエストを行う
     * https://developers.line.biz/ja/reference/messaging-api/#send-reply-message
     *
     * @param string $replyToken
     * @param MessageBuilder $messageBuilder
     * @return \Illuminate\Http\Client\Response
     */
    private function replyMessage($replyToken, MessageBuilder $messageBuilder)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN')
        ])->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages' => $messageBuilder->buildMessage(),
        ]);
    }
}
