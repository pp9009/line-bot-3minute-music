<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Constants\HTTPHeader;
use LINE\Parser\EventRequestParser;
use App\UseCases\Line\QuickReply;
use App\UseCases\Line\ReplyMusic;
use App\UseCases\Line\Share\ApiRequest;
use Artisan;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $parsedEvents = EventRequestParser::parseEventRequest($request->getContent(), env('LINE_CHANNEL_SECRET'), $request->header(mb_strtolower(HTTPHeader::LINE_SIGNATURE)));

        foreach ($parsedEvents->getEvents() as $event) {
            try {
                $getText = $event->getMessage()->getText();
                if ($getText === 'getMusic!!') {
                    $usecase = new QuickReply();
                    $usecase->invoke($event);
                } elseif (preg_match('/^[1-8]{1}分$/u', $getText)) {
                    $usecase = new ReplyMusic();
                    $usecase->invoke($event);
                } else {
                    $api = new ApiRequest();
                    $api->replyMessage(
                        $event->getReplyToken(),
                        (new TextMessage(['text' => "このBOTを使う時はメニューから\nget musicをタップしてね"]))->setType('text')
                    );
                }
            } catch (\Exception $e) {
                $api = new ApiRequest();
                $api->replyMessage(
                    $event->getReplyToken(),
                    (new TextMessage(['text' => "サーバーでエラーが発生してます。時間を置いて再度お試しください。"]))->setType('text')
                );
                throw $e;
            }

            // renderはcronが有料のためrequest毎にバッチを実行
            Artisan::call('command:getTracks');
        }
    }
}
