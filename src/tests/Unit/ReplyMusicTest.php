<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Request;
use App\UseCases\Line\ReplyMusic;
use App\UseCases\Line\Share\ApiRequest;
use Database\Seeders\UserSeeder;
use Database\Seeders\TrackSeeder;

use Illuminate\Foundation\Testing\RefreshDatabase;

class ReplyMusicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(TrackSeeder::class);

        // 受信するイベントオブジェクトを作成
        // https://developers.line.biz/ja/reference/messaging-api/#webhook-event-objects
        $this->event = [
            'type' => 'message',
            'message' => [
                'type' => 'text',
                'id' => '17085651723402',
                'text' => '3分',
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
    }

    /**
     * trackのurlを含み、「/v2/bot/message/reply」へrequestできてるか検証
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
                $request->url() == ApiRequest::REPLY_MESSAGE_ENDPOINT &&
                $request['replyToken'] == $this->event["replyToken"] &&
                str_contains($request['messages'][0]['text'], 'https://open.spotify.com/track/');
        });
    }

    /**
     * userがincrementされてるかテスト
     *
     * @return void
     */
    public function test_user_count_increment()
    {
        $usecase = new ReplyMusic();

        $usecase->invoke($this->event);

        $this->assertDatabaseHas('users', [
            'used_count' => 1,
        ]);
    }
}
