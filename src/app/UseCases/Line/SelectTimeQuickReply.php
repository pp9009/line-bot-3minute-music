<?php

namespace App\UseCases\Line;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use App\Models\User;

class SelectTimeQuickReply
{
    public function invoke($event)
    {
        User::upsert(
            ['userid' => $event->getUserId()],
            ['userid'],
        );

        $http_client = new CurlHTTPClient(env('LINE_CHANNEL_ACCESS_TOKEN'));
        $bot = new LINEBot($http_client, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);
        for ($i = 1; $i <= 8; $i++) {
            $button_list[] = new QuickReplyButtonBuilder(new MessageTemplateActionBuilder($i . '分', $i . '分'));
        }
        $bot->replyMessage(
            $event->getReplyToken(),
            new TextMessageBuilder('何分の曲にするか指定してね！', new QuickReplyMessageBuilder($button_list))
        );
    }
}
