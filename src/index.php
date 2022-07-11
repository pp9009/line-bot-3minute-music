<?php
require(__DIR__ . '/vendor/autoload.php');
include(__DIR__ . '/app/include.php');

use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

$httpClient = new CurlHTTPClient(Env::getValue('channel.access.token'));
$bot = new LINEBot($httpClient, ['channelSecret' => Env::getValue('channel.secret')]);
$signature = $_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE];
$events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);

foreach ($events as $event) {

    $text = $event->getText();
    if ($text === 'getMusic!!') {
        dbUtill::registerUser($db, $event->getUserId());
        $button_list = [];
        for ($i = 1; $i <= 8; $i++) {
            array_push($button_list, new QuickReplyButtonBuilder(new MessageTemplateActionBuilder($i . '分', $i . '分')));
        }

        $quickReply = new QuickReplyMessageBuilder($button_list);

        $bot->replyMessage(
            $event->getReplyToken(),
            new TextMessageBuilder('何分の曲にするか指定してね！', $quickReply)
        );
    } elseif (preg_match('/^[1-8]{1}分$/u', $text)) {
        dbUtill::updateUserCount($db, $event->getUserId());
        $bot->replyMessage(
            $event->getReplyToken(),
            new TextMessageBuilder(dbUtill::getMusic($db, $text))
        );
    } else {
        $bot->replyMessage(
            $event->getReplyToken(),
            new TextMessageBuilder("このBOTを使う時はメニューから\nget musicをタップしてね")
        );
    }
}
