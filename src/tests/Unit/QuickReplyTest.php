<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use App\UseCases\Line\QuickReply;
use App\UseCases\Line\Share\ApiRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuickReplyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // 受信するイベントオブジェクトを作成
        // https://developers.line.biz/ja/reference/messaging-api/#webhook-event-objects
        $this->event = [
            'type' => 'message',
            'message' => [
                'type' => 'text',
                'id' => '17085651723402',
                'text' => 'getMusic!!',
            ],
            'timestamp' => '1625665242211',
            'source' => [
                'type' => 'user',
                'userId' => config('test_data.userid'),
            ],
            'replyToken' => '757913772c4646b784d4b7ce46d12671',
            'mode' => 'active',
            'webhookEventId' => '01FZ74A0TDDPYRVKNK77XKC3ZR',
            'deliveryContext' => [
                'isRedelivery' => false,
            ],
        ];

        // 送信するメッセージオブジェクトを作成
        // https://developers.line.biz/ja/reference/messaging-api/#message-objects
        for ($i = 1; $i <= 8; $i++) {
            $actions[] = $i . '分';
        }
        $this->messages[] = (new QuickReply())->buildMessage('何分の曲にするか指定してね！', $actions);
    }

    /**
     * QuickReplyを「/v2/bot/message/reply」へrequestできてるか検証
     * https://developers.line.biz/ja/reference/messaging-api/#send-reply-message
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

        $this->assertEquals($response->status(), 200);
        Http::assertSent(function (Request $request) {
            return $request->hasHeader('Authorization', 'Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN')) &&
                $request->url() == ApiRequest::REPLY_MESSAGE_ENDPOINT &&
                $request['replyToken'] == $this->event["replyToken"] &&
                $request['messages'] == $this->messages;
        });
    }

    /**
     * userがinsertされてるか検証
     *
     * @return void
     */
    public function test_user_upsert()
    {
        $usecase = new QuickReply();

        $usecase->invoke($this->event);

        $this->assertDatabaseHas('users', [
            'id' => config('test_data.userid'),
        ]);
    }
}
