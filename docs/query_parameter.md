URLパラメータ情報
==

```
Author : Yugeta.Koji
Date   : 2019.01.01
```

# Summary
  このフレームワークのURLパラメータは、GETにいて、全て base64->urlencodeを実行してkey=valueを１文字列として格納して遷移する。
  index.phpの初期に一旦分解して、$_GETに格納する。
  クエリパタメータは、PHP以外に、javascriptでもデコード出来る関数を持つ。
  リンク作成は専用関数を容易する。

# specification
  - q=###
  queryパラメータの一括情報

# design : デザイン
  - 階層
  /design/d1/d2.html
  - クエリ
  d=%デザイン名 (default : sample)
  f=%デザインファイル名 (default : index)


# page : ページ
  - 階層
  /contents/c/p.html
  - クエリ
  c=%コンテンツ名 (default : sample)
  p=%ページ名 (default : index)

# sample
?d=project&f=login&c=project&p=test
↓
?q=ZDE9cHJvamVjdCZkMj1sb2dpbiZjPXByb2plY3QmcD10ZXN0

# howto
*.html
<a href="?q={{php:\lib\common\url::param_encode('f=test')}}">top</a><br>
