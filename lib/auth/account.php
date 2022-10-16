<?php
namespace lib\auth;
/**
 * Path    : lib/php/account.php
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.2.0
 * Summary : accountデータ管理:セキュリティログイン、ログアウト処理等
 * Example : 
 * account_entry : mail-template-file mynt/lib/config/account_entry_mail.txt
 * 
 */

class account{

  // // Login-check [browser-post] - Check Mail
	// public static function login(){

  //   if(!isset($_POST["login_pw"]) || !$_POST["login_pw"]){
  //     return false;
  //   }

  //   $mail = (isset($_POST["login_mail"])) ? $_POST["login_mail"] : "";
  //   if(!$mail){return;}

  //   $wheres = array("mail" => $mail);
  //   $res = \mynt::exec("\\mynt\\lib\\data" , "load" , array("","account",array(),$wheres));
  //   if($res["status"] === "error"){
  //     $_REQUEST["err"] = "メールアドレスまたはパスワードが違います。";
  //     return;
  //   }
  //   $data = $res["data"][0];

  //   $login_id = (isset($data["id"])) ? $data["id"] : "";
  //   $login_pw = (isset($_POST["login_pw"])) ? $_POST["login_pw"] : "";

  //   // 権限チェック
  //   if(isset($_POST["restrict_auth"]) && $_POST["restrict_auth"]){
  //     if(!isset($data["auth"]) || !$data["auth"]){
  //       $_REQUEST["err"] = "一般権限では管理機能にログインできません。";
  //       return;
  //     }
  //     $sp = explode(",",$_POST["restrict_auth"]);

  //     if(!in_array($data["auth"] , $sp)){
  //       $_REQUEST["err"] = "必要な権限が無いため、ログインできません。";
  //       return;
  //     }
  //   }

  //   // login判定
  //   if(md5($login_pw) !== $data["md5"]){
  //     $_REQUEST["err"] = "メールアドレスまたはパスワードが違います。";
  //     return;
  //   }

	// 	// success
	// 	else{
	// 		$_SESSION["login_id"] = $data["id"];
  //     $_SESSION["id"]       = $data["id"];
  //     $_SESSION["mail"]     = $data["mail"];
  //     $_SESSION["auth"]     = (isset($data["auth"])) ? $data["auth"] : "";

  //     if(isset($_REQUEST["redirect"]["success"]) && $_REQUEST["redirect"]["success"]){
  //       $url = $_REQUEST["redirect"]["success"];
  //     }
	// 		else if(isset($_REQUEST["url"]) && $_REQUEST["url"]){
  //       $url = $_REQUEST["url"];
	// 		}
	// 		else{
  //       $url = \mynt::exec("\\mynt\\lib\\url" , "getURL" , array());
  //     }

  //     \mynt::exec("\\mynt\\lib\\url" , "setURL" , array($url));
  //   }
  // }
  

  // // Louout
  // public static function logout($url=""){
  //   self::removeSession();
  //   //リダイレクト※URL指定がなければ、ログイン画面へ遷移
  //   $url = ($url) ? $url : \mynt::exec("\\mynt\\lib\\url" , "getUrl" , array());

  //   \mynt::exec("\\mynt\\lib\\url" , "setUrl" , array($url));
  // }

  // //セッション情報を削除
  // public static function removeSession(){
	// 	foreach($_SESSION as $key=>$val){
	// 		unset($_SESSION[$key]);
	// 	}
  // }
  
  // //アカウントの新規登録
  // public static function entry(){
  //   $mail = $_REQUEST["mail"];
  //   $pw1  = $_REQUEST["pw1"];
  //   $pw2  = $_REQUEST["pw2"];

  //   // check
  //   if(!$mail || !$pw1 || !$pw2){
  //     $_REQUEST["error"] = "未入力の項目があります。<br>戻るボタンを押して再入力してください。";
  //     return false;
  //   }
  //   else if($pw1 !== $pw2){
  //     $_REQUEST["error"] = "パスワードの確認が出来ません。<br>戻るボタンを押して再入力してください。";
  //     return false;
  //   }

  //   // 登録済みメールアドレスの確認
  //   $res = \mynt::exec("\\mynt\\lib\\data","load",array("","account",array(),array("mail"=>$mail)));
  //   if($res["status"] === "ok"){
  //     $_REQUEST["error"] = "すでに仕様されているメールアドレスです。";
  //     return false;
  //   }


  //   // 認証用キャッシュデータの作成（メールアドレス+ymdhis）
  //   $hash = md5($mail);
  //   $pw   = md5($pw1);

  //   // メール・メッセージ作成
  //   $url = \mynt::exec("\\mynt\\lib\\url","getUrl",array())."?p=account_mailAuth&id=".$hash;
  //   $servicename = $GLOBALS["config"]["default"]["title"];
  //   $update = date("YmdHis");

  //   $datas = array(
  //     "hash"  => $hash,
  //     "mail"  => $mail,
  //     "md5"   => $pw,
  //     "date"  => $update,
  //     "entry" => $update
  //   );

  //   // キャッシュファイルの作成
  //   \mynt::exec("\\mynt\\lib\\data" , "save" , array("" , "account_entry_cache" , $datas , array()));


  //   // メール送信
  //   $from = $GLOBALS["config"]["default"]["system_mail"];
  //   $subject = "[アカウント登録] 認証アクセスコード送信 ".$servicename;
  //   // $message = self::makeMessage($mail , $servicename , $url);
    
  //   $account_entry_mail_text = file_get_contents("mynt/lib/config/account_entry_mail.txt");
  //   $_REQUEST["account_entry"]["service_title"] = $servicename;
  //   $_REQUEST["account_entry"]["to_mail"]       = $mail;
  //   $_REQUEST["account_entry"]["auth_url"]      = $url;
  //   $message = \mynt::exec("\\mynt\\lib\\tag","conv",array($account_entry_mail_text));
  //   $res = \mynt::exec("\\mynt\\lib\\mail","sendMail",array($mail,$from,$subject,$message));

  //   // リダイレクト処理
  //   // $_REQUEST["p"] = "account_mailSend";
  //   $_REQUEST["account_mailSend"] = "sendmail";
  //   $redirect_url = \mynt::exec("\\mynt\\lib\\url","getUrl",array())."?p=account_mailSend";
  //   \mynt::exec("\\mynt\\lib\\url","setUrl",array($redirect_url));
  // }

  // // 仮登録から本登録処理（メールのリンククリック）
  // public static function hash2auth($hash=""){

  //   // ログインしている場合は、ログアウトする。
  //   if(isset($_SESSION["login_id"]) && $_SESSION["login_id"]){
  //     $redirect = \mynt::exec("\\mynt\\lib\\url","getUri");
  //     self::logout($redirect);
  //   }

  //   // chack
  //   $pw1 = $_REQUEST["pw1"];
  //   $pw2 = $_REQUEST["pw2"];
  //   if($pw1 !== $pw2){
  //     $_REQUEST["error"] = "パスワードの確認が出来ません。<br>戻るボタンを押して再入力してください。";
  //     return false;
  //   }

  //   // キャッシュデータ呼び出し
  //   $wheres = array("hash"  => $hash , "mode" => $_REQUEST["mode"]);
  //   $sort   = array("entry" => "SORT_ASC");
  //   $resLoad = \mynt::exec("\\mynt\\lib\\data" , "load" , array("" , "account_entry_cache" , array() , $wheres , $sort));
  //   if($resLoad["status"] === "error"){
  //     die("Error ! : 正常に処理できません。システム管理者までお問い合わください。");
  //   }

  //   $keys = array_keys($resLoad["data"]);
  //   $data = $resLoad["data"][$keys[count($keys)-1]];

  //   // アカウント追加処理
  //   $update = date("YmdHis");
    
  //   // password_reset
  //   if($_REQUEST["mode"] === "reset"){
  //     $dataAccount = array(
  //       "account_id" => $data["account_id"],
  //       "mail"       => $data["mail"],
  //       "auth"       => "",
  //       "md5"        => md5($pw1),
  //       "date"       => $update,
  //       "entry"      => $update
  //     );
  //   }
  //   // new-entry
  //   else if($_REQUEST["mode"] === "entry"){
  //     $dataAccount = array(
  //       "mail"   => $data["mail"],
  //       "auth"   => "",
  //       "md5"    => md5($pw1),
  //       "date"   => $update,
  //       "entry"  => $update
  //     );
  //   }
    
  //   $resSave = \mynt::exec("\\mynt\\lib\\data" , "save" , array("","account",$dataAccount,array()));
  //   if($res["status"] === "error"){
  //     die("Error ! : アカウントが正常に追加できません。システム管理者までお問い合わください。");
  //   }

  //   // キャッシュデータ削除
  //   $resDel = \mynt::exec("\\mynt\\lib\\data" , "del" , array("","account_entry_cache",array(),array($wheres)));
  //   if(\mynt::exec("\\mynt\\lib\\data","getType",array()) === "json"){
  //     $config = \mynt::exec("\\mynt\\lib\\config" , "getData" , array());
  //     $path = $config["dir"] . $config["database"] ."/account_entry_cache/".$hash.".json";
  //     if(is_file($path)){
  //       unlink($path);
  //     }
  //   }

  //   // redirect
  //   if(isset($_REQUEST["redirect"]["success"]) && $_REQUEST["redirect"]["success"]){
  //     // \mynt\lib\url::setUrl($_REQUEST["redirect"]["success"]);
  //     \mynt::exec("\\mynt\\lib\\url" , "setUrl" , array($_REQUEST["redirect"]["success"]));
  //     exit();
  //   }
  //   else{
  //     exit("Finish!!");
  //   }

  // }

  // 新規アカウント登録（メールのみ登録パターン）
  public static function mail_entry(){
    $mail = $_REQUEST["mail"];

    // check
    if(!$mail){
      $_REQUEST["error"] = "未入力の項目があります。<br>戻るボタンを押して再入力してください。";
      return false;
    }

    // 登録済みメールアドレスの確認
    $res = \mynt::data_load("","lib_account",array(),array("mail"=>$mail));
    if($res["status"] === "ok"){
      $_REQUEST["error"] = "すでに仕様されているメールアドレスです。";
      return false;
    }


    // 認証用キャッシュデータの作成（メールアドレス+ymdhis）
    $hash = md5(date("Ymd").$mail);

    // メール・メッセージ作成
    $url = \mynt::exec('\lib\common\url',"getUrl",array())."?p=system&f=plane&c=account/mail_auth"."&id=".$hash;
    $servicename = $_POST["service_name"];
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
    $from = $_POST['mail_from'];
    $subject = "[アカウント登録] 新規アカウント登録用 認証コード ".$servicename;
    
    $account_entry_mail_text = file_get_contents("lib/auth/mail_template/account_entry_mail.txt");
    $_REQUEST["account_entry"] = array(
      "service_title" => $servicename,
      "to_mail"       => $mail,
      "auth_url"      => $url
    );
    $message = \mynt::exec('\lib\html\replace',"conv",array($account_entry_mail_text));

    // メール送信
    $res = \mynt::exec('\lib\common\mail',"sendMail",array($mail,$from,$subject,$message));

    // リダイレクト処理
    if($res){
      $redirect_url = \mynt::exec('\lib\common\url',"getUrl",array())."?f=plane&c=account/account_mail_sended";
      \mynt::exec('lib\common\url',"setUrl",array($redirect_url));
    }
    else{
      $_POST["error"] = "Error! メールが送信できませんでした。";
    }
  }

  // パスワード再発行
  public static function pass_reset(){
    $mail = $_REQUEST["mail"];

    // check
    if(!$mail){
      $_REQUEST["error"] = "未入力の項目があります。<br>戻るボタンを押して再入力してください。";
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
    \mynt::exec("\\mynt\\lib\\data" , "save" , array("" , "account_entry_cache" , $datas , array()));


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

  // cache登録されたデータの取得
  public static function getHashValue($hash,$mode,$key){
    $wheres = array("hash" => $hash);
    $sort   = array("entry" => "SORT_ASC");
    $resLoad = \mynt::exec("\\mynt\\lib\\data" , "load" , array("" , "account_entry_cache" , array() , $wheres , $sort));
    if($resLoad["status"] === "error"){
      die("Error ! : 正常に処理できません。システム管理者までお問い合わください。");
    }
    $keys = array_keys($resLoad["data"]);
    $data = $resLoad["data"][$keys[count($keys)-1]];

    if(isset($data[$key])){
      return $data[$key];
    }
    else{
      return "";
    }
  }
  

}