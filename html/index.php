<?php
require(__DIR__ . '/vendor/autoload.php');
include(__DIR__ . '/private/include.php');

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
    if ('getMusic!!' === $text) {
        dbUtill::registerUser($db, $event->getUserId());
        $array = [];
        for ($i = 1; $i <= 8; $i++) {
            array_push($array, new QuickReplyButtonBuilder(new MessageTemplateActionBuilder($i . '分', $i . '分')));
        }

        $quickReply = new QuickReplyMessageBuilder($array);
        $messageTemplate = new TextMessageBuilder('何分の曲にするか指定してね！', $quickReply);

        $bot->replyMessage(
            $event->getReplyToken(),
            $messageTemplate
        );
    } elseif (preg_match('/^[1-8]{1}分$/u', $text)) {
        dbUtill::updateUserCount($db, $event->getUserId());
        $uri = dbUtill::getMusic($db, $text);
        $bot->replyMessage($event->getReplyToken(), new TextMessageBuilder($uri));
    } else {
        $bot->replyMessage(
            $event->getReplyToken(),
            new TextMessageBuilder("このBOTを使う時はメニューから\nget musicをタップしてね")
        );
    }
}
