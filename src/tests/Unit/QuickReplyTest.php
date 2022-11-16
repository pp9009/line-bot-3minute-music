<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Request;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use App\UseCases\Line\QuickReply;

class QuickReplyTest extends TestCase
{

    const REPLY_MESSAGE_ENDPOINT = LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply';

    protected function setUp(): void
    {
        parent::setUp();
        // about https://developers.line.biz/ja/reference/messaging-api/#webhook-event-objects
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
    }

    public function test_invoke()
    {
        Http::fake([
            '*' => Http::response([], 200),
        ]);
        $usecase = new QuickReply();

        $response = $usecase->invoke($this->event);

        $this->assertEquals($response->status(), 200);
        // TODO::requestの内容を検査
        Http::assertSent(function (Request $request) {
            return $request->url() == self::REPLY_MESSAGE_ENDPOINT;
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
