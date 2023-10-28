<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Request;
use LINE\Constants\HTTPHeader;
use LINE\Clients\MessagingApi\Model\TextMessage;
use App\UseCases\Line\QuickReply;
use Database\Seeders\TrackSeeder;
use Tests\TestCase;

class WebHookTest extends TestCase
{
    use RefreshDatabase;

    public const REPLY_MESSAGE_ENDPOINT = 'https://api.line.me/v2/bot/message/reply';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TrackSeeder::class);

        // 「getMusic!!」に対する返信(メッセージオブジェクト)を作成
        // https://developers.line.biz/ja/reference/messaging-api/#message-objects
        for ($i = 1; $i <= 8; $i++) {
            $actions[] = $i . '分';
        }
        $this->messages[] = (new QuickReply())->buildMessage('何分の曲にするか指定してね！', $actions);

        // 想定外の発話に対する返信(メッセージオブジェクト)を作成
        $this->errorMessages[] = (new TextMessage(['text' => "このBOTを使う時はメニューから\nget musicをタップしてね"]))->setType('text');
    }

    /**
     * 「getMusic!!」が発話されたとき、想定のQuickReplyを返信してるか検証
     *
     * @return void
     */
    public function test_quick_reply()
    {
        $request = $this->makeRequest('getMusic!!');
        $signature = $this->makeSignature($request);
        Http::fake([
            '*' => Http::response([], 200),
        ]);

        $response = $this->withHeaders([HTTPHeader::LINE_SIGNATURE => $signature])
            ->postJson('/api/webhook', $request);

        $response->assertStatus(200);
        Http::assertSent(function (Request $request) {
            return $request->hasHeader('Authorization', 'Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN')) &&
                $request->url() == self::REPLY_MESSAGE_ENDPOINT &&
                $request['messages'] == $this->messages;
        });
    }

    /**
     * 想定外のテキストが発話されたとき、エラーメッセージを返信してるか検証
     *
     * @return void
     */
    public function test_error_message()
    {
        $request = $this->makeRequest('test');
        $signature = $this->makeSignature($request);
        Http::fake([
            '*' => Http::response([], 200),
        ]);

        $response = $this->withHeaders([HTTPHeader::LINE_SIGNATURE => $signature])
            ->postJson('/api/webhook', $request);

        $response->assertStatus(200);
        Http::assertSent(function (Request $request) {
            return $request->hasHeader('Authorization', 'Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN')) &&
                $request->url() == self::REPLY_MESSAGE_ENDPOINT &&
                $request['messages'] == $this->errorMessages;
        });
    }

    /**
     * 要求されたtrackを返信してるか検証
     *
     * @return void
     */
    public function test_get_track()
    {
        $request = $this->makeRequest('3分');
        $signature = $this->makeSignature($request);
        Http::fake([
            '*' => Http::response([], 200),
        ]);

        $response = $this->withHeaders([HTTPHeader::LINE_SIGNATURE => $signature])
            ->postJson('/api/webhook', $request);

        $response->assertStatus(200);
        Http::assertSent(function (Request $request) {
            return $request->hasHeader('Authorization', 'Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN')) &&
                $request->url() == self::REPLY_MESSAGE_ENDPOINT &&
                str_contains($request['messages'][0]['text'], 'https://open.spotify.com/track/');
        });
    }

    // TODO::MessageAPIへのrequestが失敗したときの考慮

    private function makeRequest($text)
    {
        return
            [
                "destination" => "xxxxxxxxxx",
                'events' => [
                    [
                        'type' => 'message',
                        'message' => [
                            'type' => 'text',
                            'id' => '17085651723402',
                            'text' => $text,
                        ],
                        'webhookEventId' => '01FZ74A0TDDPYRVKNK77XKC3ZR',
                        'deliveryContext' => [
                            'isRedelivery' => false,
                        ],
                        'timestamp' => '1625665242211',
                        'source' => [
                            'type' => 'user',
                            'userId' => "Uxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
                        ],
                        'replyToken' => '757913772c4646b784d4b7ce46d12671',
                        'mode' => 'active',
                    ],
                ]
            ];
    }

    private function makeSignature($request)
    {
        $requestBody = json_encode($request);
        $hash = hash_hmac('sha256', $requestBody, env('LINE_CHANNEL_SECRET'), true);
        return base64_encode($hash);
    }
}
