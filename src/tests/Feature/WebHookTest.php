<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Request;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use Database\Seeders\TrackSeeder;
use Tests\TestCase;

class WebHookTest extends TestCase
{
    use RefreshDatabase;

    public const REPLY_MESSAGE_ENDPOINT = LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TrackSeeder::class);

        // 「getMusic!!」に対する返信(メッセージオブジェクト)を作成
        // https://developers.line.biz/ja/reference/messaging-api/#message-objects
        for ($i = 1; $i <= 8; $i++) {
            $buttons[] = new QuickReplyButtonBuilder(new MessageTemplateActionBuilder($i . '分', $i . '分'));
        }
        $messageBuilder = new TextMessageBuilder('何分の曲にするか指定してね！', new QuickReplyMessageBuilder($buttons));
        $this->messages = $messageBuilder->buildMessage();
        
        // 想定外の発話に対する返信(メッセージオブジェクト)を作成
        $errorMessageBuilder = new TextMessageBuilder("このBOTを使う時はメニューから\nget musicをタップしてね");
        $this->errorMessages = $errorMessageBuilder->buildMessage();
    }

    /**
     * メニューから「getMusic!!」と発話されたときのテスト
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
     * 想定外のtextが発話されたときのテスト
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
     * trackを要求されたときのテスト
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
