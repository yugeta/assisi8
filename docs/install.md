Install (auto-setting)
==

```
Author : Yugeta.Koji
Date   : 2020.01.01
```

# Summary
フレームワークの初期起動時における必要最小限の設定

# Judgement
- dataフォルダの有無

# Specification
1. dataフォルダの作成
2. configデータの作成

# flow
- dataフォルダがない場合にインストール画面に遷移
- configデータと、admin-accountを作成
- 

# Install
- JQ
ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"

# Howto ----------
- フレームワークの初期インストール方法
  1. サーバーに"myntpage"をgit-cloneする。
  2. auto.shを実行する（プラグインやライブラリの自動git-clone）
  3. ブラウザでインストールされたindex.phpにアクセスする。→インストール画面が立ち上がる。
    - 管理者用アカウントの登録と、データベース設定を行う。
  4. pageコンテンツの作成
    - サービス・コンテンツの作成
    - designパターンの切り替え

- 更新手順
  1. auto.shを実行する（プラグインやライブラリの自動git-pull）



