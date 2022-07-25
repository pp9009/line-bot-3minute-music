# line-bot-3minute-music

## DEMO
![image](https://user-images.githubusercontent.com/39284992/122329816-06f47100-cf6d-11eb-813a-4a3bcc962141.gif)  

## Setup
### LINE
[LINE Developers](
https://developers.line.biz/ja/docs/messaging-api/getting-started/)からMessaging APIの`CHANNEL_ACCESS_TOKEN/CHANNEL_SECRET`を取得  

### Spotify
[Spotify for Developers](https://developer.spotify.com/dashboard/)からアプリケーションの`CLIENT_ID/CLIENT_SECRET`を取得

```
# .env

# line
LINE_CHANNEL_ACCESS_TOKEN=""
LINE_CHANNEL_SECRET=""

# spotify
SPOTIFY_CLIENT_ID=""
SPOTIFY_CLIENT_SECRET=""
```

### Webhookの設定

サーバーを外部に公開して、LINE Messaging APIにWebhook URLを設定
#### ngrokを用いる場合
1. [ngrok dashboard](https://dashboard.ngrok.com/get-started/your-authtoken)からAuthtokenを取得  
    ```
    # .env
    NGROK_AUTH=""
    ```
2. http://localhost:4040 から公開URLを取得
3. [LINE Messaging APIにWebhook URLを設定](https://developers.line.biz/ja/docs/messaging-api/building-bot/#setting-webhook-url)

## Usage
1. コンテナを実行
```
$ docker-compose up -d
```
2. composer install
```
$ docker exec -it line_bot_php bash
$ composer install
```
3. start local development server  
```
$ php -S 0.0.0.0:80 -t public
```
4. LINE BOTに「getMusic!!」を発話
