<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuickReplyTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_api_request()
    {
        // about https://developers.line.biz/ja/reference/messaging-api/#webhook-event-objects
        $response = $this->postJson('/api/webhook', [
            'destination' => 'xxxxxxxxxx',
            'events' => [
                'type' => 'message',
                'message' => [
                    'type' => 'text',
                    'id' => '17085651723402',
                    'text' => 'getMusic!!',
                ],
                'timestamp' => '1625665242211',
                'source' => [
                    'type' => 'user',
                    'userId' => 'Uxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                ],
                'replyToken' => '757913772c4646b784d4b7ce46d12671',
                'mode' => 'active',
                'webhookEventId' => '01FZ74A0TDDPYRVKNK77XKC3ZR',
                'deliveryContext' => [
                    'isRedelivery' => false,
                ],
            ],
        ]);

        // TODO :: responseの検証
        $response->assertStatus(200);
    }
}
