<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\UseCases\Line\QuickReply;
use Illuminate\Support\Facades\Http;
use LINE\LINEBot\Event\MessageEvent\TextMessage;

class QuickReplyTest extends TestCase
{
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
                    'userId' => "U131d43d529f145156285155433dd9dcc",
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

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_invoke()
    {
        Http::fake([
            '*' => Http::response([], 200),
        ]);
        $usecase = new QuickReply();

        $response = $usecase->invoke($this->event);

        $this->assertEquals($response->getHTTPStatus(), 200);
    }
}
