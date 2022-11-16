<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Request;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use App\UseCases\Line\QuickReply;

class QuickReplyTest extends TestCase
{
    public const REPLY_MESSAGE_ENDPOINT = LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply';

    protected function setUp(): void
    {
        parent::setUp();
        // 受信するイベントオブジェクトを作成 
        // https://developers.line.biz/ja/reference/messaging-api/#webhook-event-objects
        $this->event = new TextMessage(
            [
                'type' => 'message',
                'message' => [
                    'type' => 'text',
                    'id' => '17085651723402',
                    'text' => 'getMusic!!',
                ],
                'timestamp' => '1625665242211',
                'source' => [
                    'type' => 'user',
                    'userId' => "Uxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
                ],
                'replyToken' => '757913772c4646b784d4b7ce46d12671',
                'mode' => 'active',
                'webhookEventId' => '01FZ74A0TDDPYRVKNK77XKC3ZR',
                'deliveryContext' => [
                    'isRedelivery' => false,
                ],
            ]
        );

        // 送信するメッセージオブジェクトを作成
        // https://developers.line.biz/ja/reference/messaging-api/#message-objects
        for ($i = 1; $i <= 8; $i++) {
            $button_list[] = new QuickReplyButtonBuilder(new MessageTemplateActionBuilder($i . '分', $i . '分'));
        }
        $message_builder = new TextMessageBuilder('何分の曲にするか指定してね！', new QuickReplyMessageBuilder($button_list));
        $this->messages = $message_builder->buildMessage();
    }

    public function test_invoke()
    {
        Http::fake([
            '*' => Http::response([], 200),
        ]);
        $usecase = new QuickReply();

        $response = $usecase->invoke($this->event);

        $this->assertEquals($response->status(), 200);
        Http::assertSent(function (Request $request) {
            return $request->hasHeader('Authorization', 'Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN')) &&
                $request->url() == self::REPLY_MESSAGE_ENDPOINT &&
                $request['replyToken'] == $this->event->getReplyToken() &&
                $request['messages'] == $this->messages;
        });
    }

    public function test_user_upsert()
    {
        $usecase = new QuickReply();

        $usecase->invoke($this->event);

        $this->assertDatabaseHas('users', [
            'userid' => 'Uxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        ]);
    }
}
