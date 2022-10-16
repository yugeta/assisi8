<?php
namespace page\system\contents\debug;

class mail{
  public static function check_post(){
    // die($_POST["mail"]);
    $from = $_POST["from"];
    if(!$from){
      $_POST["message"] = "Error ! code:004 (システムメールの設定ができていません。)";
      return;
    }
    $mail = $_POST["mail"];

    $mail_subject = "[送信テスト]";
    $mail_text = json_encode($_SERVER,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);

    $res = \mynt::exec('\lib\mail\common','sendMail',array($mail , $from , $mail_subject , $mail_text));
    if($res){
      $_POST["message"] = "正常に送信されました。";
    }
    
  }
}