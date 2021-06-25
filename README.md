# line-bot-3minute-music
![image](https://user-images.githubusercontent.com/39284992/122329816-06f47100-cf6d-11eb-813a-4a3bcc962141.gif)  
[投稿したQiitaの記事](https://qiita.com/PP_/items/7eec02d645f0ae52ede0)

# Usage

```
git clone https://github.com/pp9009/line-bot-3minute-music.git
cd line-bot-3minute-music
docker-compose up -d --build
```


BOTをローカルで動作させる場合は、下記を行う。

* [LINE Developers](
https://developers.line.biz/ja/docs/messaging-api/getting-started/)からBOT用のMessaging APIを作成
  * Messaging APIの{channel.access.token}{channel.secret}を/private/conf/.envに記載

* [Spotify for Developers](https://developer.spotify.com/dashboard/)からAPIをコールするクライアントアプリケーションを登録
  * アプリケーションの{client.id}{client.secret}をprivate/conf/.envに記載

* webhookの設定
  * [ngrok](https://ngrok.com/)など使用し、開発サーバーを外部に公開
    * [BOTにWebhook URLを設定する](https://developers.line.biz/ja/docs/messaging-api/building-bot/#setting-webhook-url)

上記を行い、/private/get_music.phpを実行してspotifyURIを取得。
BOTで「getMusic!!」と発話すると動作確認ができます。
