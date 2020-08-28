<?php
require(__DIR__ . '/vendor/autoload.php');
include(__DIR__ . '/private/include.php');

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(Conf::getValue('line', 'channel.access.token'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => Conf::getValue('line', 'channel.secret')]);

$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

$events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);

foreach ($events as $event) {
    // メッセージを返信test
    $uri = dbUtill::getMusic($db, true);

    $response = $bot->replyMessage(
        $event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($uri)
    );
}