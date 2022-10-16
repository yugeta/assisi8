page仕様
==

# Summary
- WEB表示に関する情報（フロントエンジニア構築領域）
- "p=***"で指定されたページを表示する仕様

# 構成
- ディレクトリ構造
/page/%page-project/***
プロジェクト以下の階層は、自由に設定できるが、以下のファイルは固定で設置する。

```
page/
└ %project-name/
  ├ index.html
  └ setting.json
    contents/
    └ index.html
```

## page/%project-name/index.html
- htmlの基本構造の指定ファイル

## page/%project-name/contents/index.html
- htmlのbody内部のコンテンツ表示を記述するファイル

## page/%project-name/setting.json
デザイン変更機能を利用する場合に設定する。
※ "design/%design-project-name/"を指定することができる。
```
{
  "design" : "plane"
}
```

# design仕様について
- design-projectのbody（表示エレメント）内をpage-projectで表示することができる。
- design-project内のheadタグや、その他任意のタグをpage内の任意ファイルで設定することが可能。
  1. head-tag : ヘッドタグ内に記述
  2. body-start : body開始直後に記述
  3. body-end : body終了直前に記述
- または、css,jsのフロントデザインに関するモジュールを自動読み込みできる。

# ソース記述
- 通常表示
{{php:\mynt::page({{get:p}})}}

- systemのroot-index表示
{{php:\mynt::page("","system")}}

- 


