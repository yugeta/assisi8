<?php
namespace lib\auth;
/**
 * Path    : lib/php/session.php
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.1.0
 * Summary : HTMLタグに記述するプログラム処理 Replacement-Tag(RepTag) mynt-format
 * Example : {{method:\mynt\lib\tag::test("value")}}
 */

class session{

  // Start-Session
	public static function start(){
    $config = \mynt::exec("\\lib\\data\\database" , "getSetting" , array());

    // Make-Directory
    if(isset($config["session_path"]) && $config["session_path"]){
      $session_path = $config["session_path"];
      if(!is_dir($session_path)){mkdir($session_path , 0744 , true);}
      ini_set('session.save_path', $session_path);  // 保存先
    }
    
    // Session-start
    if(isset($config["session"]) && $config["session"]){
      session_name($config["session"]);
      session_start();
    }
	}

  // adminユーザーのみログイン有効（adminではない場合はログアウトする）
	public static function check_auth($redirect1="" , $redirect2=""){
    // ログインしている場合
    if(isset($_SESSION["login_id"]) && $_SESSION["login_id"] && $redirect1){
      \mynt::exec('\lib\common\url','setUrl',array($redirect1));
    }
    // // ログインしていてadminユーザー
		// else if(isset($_SESSION["auth"]) && $_SESSION["auth"] === "admin"){
    //   // return true;
    // }
    // ログインしていない場合
    else if((!isset($_SESSION["login_id"]) || !$_SESSION["login_id"]) && $redirect2){
      \mynt::exec('\lib\common\url','setUrl',array($redirect2));
    }
  }

  //セッション情報を削除
  public static function removeSession(){
    $_SESSION = array();
    $session_name = $GLOBALS["database"]["session"];
    if (isset($_COOKIE[$session_name])) {
      setcookie($session_name, '', time() - 1800, '/');
    }
		// foreach($_SESSION as $key=>$val){
		// 	unset($_SESSION[$key]);
		// }
  }
}