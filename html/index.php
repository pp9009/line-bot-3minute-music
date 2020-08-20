<?php
require (__DIR__ . '/vendor/autoload.php');
include (__DIR__ . '/private/include.php');

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('/Ws3S+pBmjmW2p25uy3OJGZ/PsoDMt2XNfdeYdLj7P/mZ8sxF5shGIHzc4UJZjnuKKnNViYbixWMLVs2qOEM4QWHXGQOIQCM7EX7v0jPJAP0cnGIXBvGy9kcBnyPbDIjfBe/MdizAQTmcZ/a8CP4uwdB04t89/1O/w1cDnyilFU=');
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '6522ee5fa58081de5ac83dd384957b69']);

$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

$events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);

foreach ($events as $event) {
    // メッセージを返信
    $uri = dbUtill::getMusic($db,true);

    $response = $bot->replyMessage(
        $event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($uri)
    );
}