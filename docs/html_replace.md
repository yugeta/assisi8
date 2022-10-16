HTML replace
==

[README](../README.md) > System

```
Author : Yugeta.Koji
Date   : 2020.01.01
```

# Summary
htmlファイル内に記述してphpを実行できる記述システム

# program
- replace.php


# type
- クエリ
  格納されている文字列のみを受け取れる
  {{request:page}}
    $_REQUEST["page"]
  {{post:page}} 
    $_POST["page"]
  {{get:page}}
    $_GET["page"]
  {{globals:test}}
    $GLOBALS["test"]
  {{define:hoge}}
    constant("hoge");
  {{session:id}}
    $_SESSION["id"]
  {{server:host}}
    $_SERVER["host"]
  {{config:version}}
    $GLOBALS["config"]["version"]

- php関数起動
  {{php::\lib\common\url::setUrl("http://example.com")}}
  php内で記述する方式と同じようにphpを実行できます。
  ※ returnで返ってきた値を文字列として受け取れます。

- file読み込み
  {{file}}
  * 内包されるreplaceも実行されます。

- if文
  - ifパターン
  {{if:"{{get:p}}" === "index"}}
    {{php:\main::test()}}
  {{if}}

  - if/elifパターン
  {{if:"{{get:p}}" === "index"}}
    {{php:\main::index()}}
  {{elif:"{{get:p}}" === "test"}}
    {{php:\main::test()}}
  {{if}}

  - if/elseパターン
  {{if:"{{get:p}}" === "index"}}
    {{php:\main::index()}}
  {{else}}
    {{php:\main::etc()}}
  {{if}}

  - if/elif/elseパターン
  {{if:"{{get:p}}" === "index"}}
    {{php:\main::index()}}
  {{elif:"{{get:p}}" === "test"}}
    {{php:\main::test()}}
  {{else}}
    {{php:\main::etc()}}
  {{if}}

- for文
  {{for:}}

  {{}}

