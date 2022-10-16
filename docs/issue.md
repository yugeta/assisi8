
# 2020.09.05

- php command-web

  ## db新規登録操作
  Warning: file_get_contents(data/shop/lib_setting.json): failed to open stream: No such file or directory in /Users/yugeta/web/myntpage/lib/data/data_json.php on line 248
  -> fix

  Warning: file_get_contents(data/shop/lib_setting.json): failed to open stream: No such file or directory in /Users/yugeta/web/myntpage/lib/data/data_json.php on line 248
  -> fix

  Warning: ini_set(): Headers already sent. You cannot change the session module's ini settings at this time in /Users/yugeta/web/myntpage/lib/auth/session.php on line 22

  Warning: session_name(): Cannot change session name when headers already sent in /Users/yugeta/web/myntpage/lib/auth/session.php on line 27

  Warning: session_start(): Cannot start session when headers already sent in /Users/yugeta/web/myntpage/lib/auth/session.php on line 28

  Warning: Cannot modify header information - headers already sent by (output started at /Users/yugeta/web/myntpage/lib/data/data_json.php:248) in /Users/yugeta/web/myntpage/lib/url/common.php on line 78
  -> fix???

# 2020.10.09
  ## \mynt::data_loadの時に、複数indexがある場合に、全部検索ができない
  - indexが"_"で区切られて処理されるため、ファイル名が特定されないとnot-foundになる。
  - ただし、index値がtable内に1つの場合は、問題なく全件検索できる。
  
# 2020.10.11
  ## html埋め込みif文で「if-elif-/if」が使えない
  - 「if-elif-else-/if」としなければエラーになる。（今後改修予定）
