GoogleAPIの使い方
==
```
Author : Yugeta.Koji
Date   : 2020.07.16
```

# Summary
  Google SpreadSheetをphpからデータ取得、書き換えなどが行える

# Init setting
  Google Documentの書類のパーミッション設定を事前にしておく必要がある。
  - 手順
  1. Google APIS developer consoleサイトにアクセスして、プロジェクトを作成
    url : https://console.developers.google.com/flows/enableapi?apiid=sheets.googleapis.com

 

  2. 「プロジェクトを作成」を選択して、「続行」ボタンを押す。

  3. APIを有効にする。
    ※「API は有効になっています」となっていれば次に進む
    「認証情報に進む」ボタンを押す

  4. 「認証方法」タブの以下を設定する
    - Google Sheets API
    - ウェブサーバー(nodejs,tomcatなど)
    - アプリケーションデータ
    - AppEngine 「いいえ、使用していません。」を選択
    - 「必要な認証情報」ボタンを押す。

  5. プロジェクトへの認証情報の追加
    - サービス アカウントを作成する
      - アカウント名を入力
      - ロール（役割）を選択 : オーナー、編集者、閲覧者等
      - キータイプ : json
    - 「次へ」ボタンを押す

  6. サービスアカウントキーファイルが作成される。
    - 作成されたjsonファイルのダウンロード data/auth/.../**.json　に保存
    - 「閉じる」を押す

  7. サービスアカウントに表示されている、メールアドレスをコピー

  8. スプレッドシートの共有
    - 書き込みをしたいGoogleSheetのファイルを開き、「共有」ボタンを押す
    - 「ユーザーやグループを追加」にコピーしたメールアドレスを入力してenterを押す。

  9. プロジェクト名を変更したい場合は、AMIページで変更できる。



# Edit setting]

 - Google APIS developer consoleサイトにアクセスして、設定編集をする。
    url : https://console.developers.google.com/apis/dashboard



# Refelense
  - [PHP] Google Sheets API を使ってスプレッドシートに値を書き込む
  https://agohack.com/writing-values-using-google-sheets-api-php/