<?php
$root = dirname(__FILE__);
if(is_file("/lib/main/init.php")){
  require_once $root . "/lib/main/init.php";
}

// 受け渡し用変数
$GLOBALS["__dir__"] = $root;

echo "test";