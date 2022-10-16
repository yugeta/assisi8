<?php

require_once "lib/main/init.php";
class mynt extends \lib\main\init {}

// クエリパラメータのデコード処理 ( q -> $_GET[key=value] )
\mynt::queryParameters();

// check-install : データがない場合は、インストール画面へ遷移
if(!\mynt::exec('\lib\install\check',"data")){
  \mynt::method();
  \mynt::page("install","system");
  exit();
}

// Load-page-Setting (in $GLOBALS["config"])
\mynt::exec('\lib\page\setting' , 'load_global');

\mynt::exec('\lib\page\setting' , 'setGlobals');

// Session-Start
\mynt::exec('\lib\auth\session' , 'start');

// check-method
\mynt::method();

$_POST["p"] = $_GET["p"] = $_REQUEST["p"] = "system";

// view
\mynt::page();

exit();
