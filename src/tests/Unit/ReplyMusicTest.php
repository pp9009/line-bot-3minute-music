<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Request;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use App\UseCases\Line\ReplyMusic;
use Database\Seeders\TrackSeeder;

use Illuminate\Foundation\Testing\RefreshDatabase;

class ReplyMusicTest extends TestCase
{
    use RefreshDatabase;

    public const REPLY_MESSAGE_ENDPOINT = LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TrackSeeder::class);

        // 受信するイベントオブジェクトを作成
        // https://developers.line.biz/ja/reference/messaging-api/#webhook-event-objects
        $this->event = new TextMessage(
            [
                'type' => 'message',
                'message' => [
                    'type' => 'text',
                    'id' => '17085651723402',
                    'text' => '3分',
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

    /**
     * 返信にtrackのurlを含み、「/v2/bot/message/reply」へrequestできてるかテスト
     * https://developers.line.biz/ja/reference/messaging-api/#send-reply-message
     *
     * @return void
     */
    public function test_invoke()
    {
        Http::fake([
            '*' => Http::response([], 200),
        ]);
        $usecase = new ReplyMusic();

        $response = $usecase->invoke($this->event);

        $this->assertEquals($response->status(), 200);
        Http::assertSent(function (Request $request) {
            return $request->hasHeader('Authorization', 'Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN')) &&
                $request->url() == self::REPLY_MESSAGE_ENDPOINT &&
                $request['replyToken'] == $this->event->getReplyToken() &&
                str_contains($request['messages'][0]['text'], 'https://open.spotify.com/track/');
        });
    }
}
