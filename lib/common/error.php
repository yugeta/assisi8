<?php
/**
* エラー表示処理
*/
namespace lib\common;
class error{
  public static function view($message="",$module_path="",$code=""){
    echo "<pre>";
    echo "<h1>Error !!!</h1>".PHP_EOL;
    echo "<hr>";
    echo PHP_EOL;

    echo "Message : ".$message;
    echo "<hr>";
    echo PHP_EOL;

    echo "Module-path : ".$module_path;
    echo "<hr>";
    echo PHP_EOL;

    echo "Error-code : ".$code;
    echo "<hr>";
    echo PHP_EOL;

    echo "ブラウザの「戻る」ボタンを押してください。";
    echo "</pre>";
    exit();
  }

  function view_error($str=""){
    return $str;
  }
}
