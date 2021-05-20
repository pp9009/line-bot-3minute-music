<?php

use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

require(__DIR__ . '/vendor/autoload.php');
include(__DIR__ . '/private/include.php');

$httpClient = new CurlHTTPClient(Conf::getValue('line', 'channel.access.token'));
$bot = new LINEBot($httpClient, ['channelSecret' => Conf::getValue('line', 'channel.secret')]);
$signature = $_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE];
$events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);

foreach ($events as $event) {

    $text = $event->getText();
    if ('getMusic!!' === $text) {
        $array = [];
        for ($i = 1; $i <= 8; $i++) {
            array_push($array, new QuickReplyButtonBuilder(new MessageTemplateActionBuilder($i . '分', $i . '分')));
        }
        $quickReply = new QuickReplyMessageBuilder($array);
        $messageTemplate = new TextMessageBuilder('曲の分数を指定してね！', $quickReply);
        $d = $messageTemplate->buildMessage();

        $response = $bot->replyMessage(
            $event->getReplyToken(), $messageTemplate);

    } elseif (preg_match('/^[1-8]{1}分$/u', $text)) {
        $uri = dbUtill::getMusic($db, $text, true);
        $response = $bot->replyMessage($event->getReplyToken(), new TextMessageBuilder($uri));
    }
}