data
==

# Summary
- dataの各種設定を行う方法
- database名を初期セットする。(default : myntpage)
- data-tablesをシステム起動時にセットする必要がある。
  - 初期必要なtableは、accountとproperty

# json設定

- lib/data/database.json
  - type
    保存データタイプ
    - json
    - mysql
    - sqlite (今後搭載予定)
    - net (今後搭載予定)

  - dir
    データ格納用 (固定 : data)
    ※ 基本的に変更不可
    ※ json以外の場合でも、画像や各種素材の保存で利用する
    data/%database/*

  - database
    データベース名（jsonの場合は、保存ディレクトリ）
    dir/database/*


  - addr
    "type":"net"の場合にセットする。
    通信先（親）アドレス
    http://machida.yugeta.xyz/data/net.php
    ※各種認証等はクエリにつけるhash文字列とする。
    ※以後、仕様追加予定

  - host,port,user,pass
    mysqlへのアクセス設定
    "host" : "localhost",
    "port" : "3306",
    "user" : "root",
    "pass" : "root",

  - session_name
    データ保存用ディレクトリ名
    未記入の場合は、データベース名となる。
    
  - session_path
    データ格納パス（システム設定のままでいい場合は、無記述でOK）

    

- tables/*.json
  各種テーブル設定
  data/**/tables/内の情報のみ編集可能
  lib/*/tables/*.json
```
{
  "info" : {
    "name" : "テーブル名", // 表示用
    "type" : "add",  // [ add（追記型） , static（単一上書き型） , overwrite（ID固定上書き） ]※json形式のみの対応
    "mode" : "system" // 未使用
  }
  
  "columns" : {
    "id":{
      "name"   : "連番 xxxデータID",  // 表示用
      "type"   : "INT", // データ型 [ INT , VARCHAR , TEXT ]
      "length" : 11,  // データ要領
      "option" : "UNSIGNED AUTO_INCREMENT PRIMARY KEY",  // 設定オプション
      "index"  : "uid" // json利用の場合は、directoryになり、データ効率化される。設定ファイルに記述された順番で階層化される。（同じindex値の場合は連結して1階層になる）
    },
    ...
  }
}
```

