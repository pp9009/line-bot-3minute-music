<?php

namespace App\UseCases\Line;

use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
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
            ['id' => $event->getUserId()],
            ['id'],
        );

        for ($i = 1; $i <= 8; $i++) {
            $button_list[] = new QuickReplyButtonBuilder(new MessageTemplateActionBuilder($i . '分', $i . '分'));
        }

        $api = new ApiRequest();
        return $api->replyMessage(
            $event->getReplyToken(),
            new TextMessageBuilder('何分の曲にするか指定してね！', new QuickReplyMessageBuilder($button_list))
        );
    }
}
