<?php
namespace page\system\contents\account;

class reset{

  // 新規アカウント登録（メールのみ登録パターン）
  public static function post(){
    $mail = $_POST["mail"];

    // check
    if(!$mail){
      $_POST["error"] = "未入力の項目があります。<br>再入力してください。";
      return false;
    }
    else if(!preg_match("/^[a-zA-Z0-9_.+-]+@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/" , $mail)){
      $_POST["error"] = "メール形式で入力してください。";
      return false;
    }

    // // 登録済みメールアドレスの確認
    // $res = \mynt::data_load("","lib_account",array(),array("mail"=>$mail));
    // if($res["status"] === "ok"){
    //   $_POST["error"] = "すでに使用されているメールアドレスです。";
    //   return false;
    // }


    // 認証用キャッシュデータの作成（メールアドレス+ymdhis）
    $hash = md5(date("Ymd").$mail);

    // メール・メッセージ作成
    $querys = array("p=system","f=plane","c=account/auth","id=".$hash);
    $query  = \mynt::exec('\lib\url\query','param_encode',array(join("&",$querys)));
    $url = \mynt::exec('\lib\common\url',"getUrl",array())."?q=".$query;
    $servicename = $GLOBALS["page"]["name"];
    $update = date("YmdHis");

    $datas = array(
      "hash"  => $hash,
      "mode"  => "entry",
      "mail"  => $mail,
      "entry" => $update
    );

    // キャッシュファイルの作成
    \mynt::data_save("" , "lib_account_entry_cache" , $datas , array());

    // メール送信準備
    // $from = isset($_POST['mail_from']) ? $_POST['mail_from'];
    $setting_res = \mynt::data_load("","lib_setting");
    if($setting_res["status"] === "ok" && isset($setting_res["data"]["mail"]) && $setting_res["data"]["mail"]){
      $from = $setting_res["data"]["mail"];
    }
    else if(isset($_POST['mail_from']) && $_POST['mail_from']){
      $from = $_POST['mail_from'];
    }
    else if(isset($GLOBALS["page"]["mail"]) && $GLOBALS["page"]["mail"]){
      $from = $GLOBALS["page"]["mail"];
    }
    else{
      die("Error ! Fail Mail-server-setting. (system/account/entry/post) check-mail:data/*/lib_setting.json");
    }


    $subject = "パスワードリセット用 認証コード ".$servicename;
    
    $account_entry_mail_text = file_get_contents("page/system/contents/account/template/account_entry_mail.txt");
    $_POST["account_entry"] = array(
      "service_title" => $servicename,
      "to_mail"       => $mail,
      "auth_url"      => $url,
      "code"          => $hash
    );
    $message = \mynt::exec('\lib\html\replace',"conv",array($account_entry_mail_text));

    // メール送信
    $res = \mynt::exec('\lib\mail\common',"sendMail",array($mail,$from,$subject,$message));

    // リダイレクト処理
    if(!$res){
      $_POST["error"] = "Error! メールが送信できませんでした。";
      return;
    }

    $querys   = array("p=system","f=plane","c=account/auth");
    $query    = \mynt::exec('\lib\url\query','param_encode',array(join("&",$querys)));
    $next_url = \mynt::exec('\lib\common\url',"getUrl",array())."?q=".$query;
    \mynt::exec('lib\common\url',"setUrl",array($next_url));

  }
  
  public static function post_code(){
    $code = $_POST["code"];

    // キャッシュデータ呼び出し
    $wheres = array("hash"  => $code , "mode" => $_REQUEST["mode"]);
    $sort   = array("entry" => "SORT_ASC");
    $resLoad = \mynt::data_load($GLOBALS["page"]["page"] , "lib_account_entry_cache" , array() , $wheres , $sort);
    if($resLoad["status"] === "error"){
      die("Error ! : 正常に処理できません。システム管理者までお問い合わください。");
    }

    //認証コードチェック
    if($code !== $resLoad["data"][0]["hash"]){
      $_POST["message"] = "Error ! 認証コードが違います。";
      return;
    }

    $querys   = array("p=system","f=plane","c=account/pass","id=".$code);
    $query    = \mynt::exec('\lib\url\query','param_encode',array(join("&",$querys)));
    $next_url = \mynt::exec('\lib\common\url',"getUrl",array())."?q=".$query;
    \mynt::exec('lib\common\url',"setUrl",array($next_url));
  }

  // 仮登録から本登録処理（メールのリンククリック）
  public static function post_pass(){

    $hash = $_GET["id"];

    // chack
    if(!isset($_POST["pw1"]) || !isset($_POST["pw2"])){
      $_POST["error"] = "system error !!";
      return false;
    }
    $pw1 = $_POST["pw1"];
    $pw2 = $_POST["pw2"];
    if($pw1 === "" || $pw2 === ""){
      $_POST["error"] = "未入力の項目があります。";
      return false;
    }
    if($pw1 !== $pw2){
      $_POST["error"] = "パスワードの確認が出来ません。<br>再入力してください。";
      return false;
    }

    // キャッシュデータ呼び出し
    $wheres = array("hash"  => $hash , "mode" => $_REQUEST["mode"]);
    $sort   = array("entry" => "SORT_ASC");
    $resLoad = \mynt::data_load("" , "lib_account_entry_cache" , array() , $wheres , $sort);
    if($resLoad["status"] === "error"){
      die("Error ! : 正常に処理できません。システム管理者までお問い合わください。");
    }

    $keys = array_keys($resLoad["data"]);
    $data = $resLoad["data"][$keys[count($keys)-1]];

    // アカウント追加処理
    $update = date("YmdHis");

    // password_reset
    if($_REQUEST["mode"] === "reset"){
      $dataAccount = array(
        "account_id" => $data["account_id"],
        "mail"       => $data["mail"],
        "auth"       => "",
        "pass"       => \mynt::exec('\lib\auth\password','encode',array($pw1)),
        "entry"      => $update
      );
    }
    // new-entry
    else if($_REQUEST["mode"] === "entry"){
      $dataAccount = array(
        "id"         => \mynt::exec('\lib\auth\uuid\UUID','v4'),
        "mail"   => $data["mail"],
        "auth"   => "",
        "pass"   => \mynt::exec('\lib\auth\password','encode',array($pw1)),
        "entry"  => $update
      );
    }
    
    $resSave = \mynt::data_save("","lib_account",$dataAccount);
    if($resSave["status"] === "error"){
      die("Error ! : アカウントが正常に追加できません。システム管理者までお問い合わください。");
    }

    // キャッシュデータ削除
    $resDel = \mynt::data_del("","lib_account_entry_cache",array(),array($wheres));
    $dbSetting = \mynt::exec('\lib\data\database',"getSetting");
    if(isset($dbSetting["type"]) && $dbSetting["type"] === "json"){
      $dir      = \mynt::exec('\lib\data\database','getDir');
      $path = $dir . $dbSetting["database"] ."/lib_account_entry_cache/".$hash.".json";
      if(is_file($path)){
        unlink($path);
      }
    }

    // redirect
    if(isset($_POST["redirect"]) && $_POST["redirect"]){
      // \mynt\lib\url::setUrl($_REQUEST["redirect"]["success"]);
      \mynt::exec('\lib\url\common' , "setUrl" , array($_POST["redirect"]));
      exit();
    }
    else{
      exit("Finish!!");
    }

  }

  // Louout
  public static function logout($url=""){
    self::removeSession();
    //リダイレクト※URL指定がなければ、ログイン画面へ遷移
    $url = ($url) ? $url : \mynt::exec('\lib\url\common' , "getUrl");

    \mynt::exec('\lib\url\common' , "setUrl" , array($url));
  }




  // パスワード再発行
  public static function pass_reset(){
    $mail = $_REQUEST["mail"];

    // check
    if(!$mail){
      $_REQUEST["error"] = "未入力の項目があります。<br>再入力してください。";
      return false;
    }

    // mail -> account_id
    $res = \mynt::exec("\\mynt\\lib\\data","load",array("","account",array(),array("mail"=>$mail)));
    if($res["status"] === "error"){
      die("Error !!!");
    }
    $resData = $res["data"][0];


    // 認証用キャッシュデータの作成（メールアドレス+ymdhis）
    $hash = md5(date("Ymd").$mail);

    // メール・メッセージ作成
    $url = \mynt::exec("\\mynt\\lib\\url","getUrl",array())."?p=account_pass_reset_auth&id=".$hash;
    $servicename = $GLOBALS["config"]["default"]["title"];
    $update = date("YmdHis");

    $datas = array(
      "account_id" => $resData["id"],
      "hash"       => $hash,
      "mode"       => "reset",
      "mail"       => $mail,
      "entry"      => $update
    );

    // キャッシュファイルの作成
    \mynt::exec("\\mynt\\lib\\data" , "save" , array("" , "lib_account_entry_cache" , $datas , array()));


    // メール送信準備
    $from = $GLOBALS["config"]["default"]["system_mail"];
    $subject = "[アカウント登録] パスワード再発行用 認証コード ".$servicename;
    
    $account_entry_mail_text = file_get_contents("mynt/lib/config/account_pass_reset.txt");
    $_REQUEST["account_entry"] = array(
      "service_title" => $servicename,
      "to_mail"       => $mail,
      "auth_url"      => $url
    );
    $message = \mynt::exec("\\mynt\\lib\\tag","conv",array($account_entry_mail_text));

    // メール送信
    $res = \mynt::exec("\\mynt\\lib\\mail","sendMail",array($mail,$from,$subject,$message));

    // リダイレクト処理
    $_REQUEST["account_mailSend"] = "sendmail";
    $redirect_url = \mynt::exec("\\mynt\\lib\\url","getUrl",array())."?p=account_mail_sended";
    \mynt::exec("\\mynt\\lib\\url","setUrl",array($redirect_url));
  }

  
  

}