<?php
namespace lib\auth;

class login{
  public static function login(){
    $mail     = $_POST["mail"];
    $pass     = $_POST["pass"];
    $query_ok = $_POST["redirect"]["ok"];
    $query_ng = $_POST["redirect"]["ng"];

    $check = self::login_error($mail,$pass);
    switch($check["error"]){
      // login成功
      case null:
        // ログイン成功
        self::login_success($check["data"]["data"][0]);
        // Redirect処理
        self::redirect_query($query_ok);
        break;

      // login失敗
      case 1:
        // self::view("Error : 未入力の項目があります。(code:1)",$query_ng);
        $_POST["error"] = "Error : 未入力の項目があります";
        return;
        break;
      case 2:
        // self::view("Error : メールアドレスまたはパスワードが違います。(code:2)",$query_ng);
        $_POST["error"] = "Error : メールアドレスまたはパスワードが違います。";
        return;
        break;
      case 3:
        // self::view("Error : 必要な権限が無いため、ログインできません。(code:3)",$query_ng);
        $_POST["error"] = "Error : 必要な権限が無いため、ログインできません。";
        return;
        break;
      case 4:
        // self::view("Error : メールアドレスまたはパスワードが違います。(code:4)",$query_ng);
        $_POST["error"] = "Error : メールアドレスまたはパスワードが違います";
        return;
        break;
      
      default :
        // die("Error : system-error.");
        $_POST["error"] = "Error : system-error.";
        return;
        break;
    }
  }

  public static function login_error($mail="",$pass=""){

    $check = array(
      "error"  => null,
      "data" => null
    );

    // 未入力チェック
    if(self::check_exist($mail,$pass) === false){
      $check["error"] = 1;
      return $check;
    }

    // データload
    if($check["error"] === null){
      $data = self::load($mail);

      // mailチェック
      if(!$data || self::check_mail($data) === false){
        $check["error"] = 2;
      }

      // 権限チェック
      $auth = (isset($data["data"][0]["auth"])) ? $data["data"][0]["auth"] : "";
      if(self::check_auth($auth) === false){
        $check["error"] = 3;
      }

      // 旧版(md5パスワード)対応
      if(!isset($data["data"][0]["pass"]) && isset($data["data"][0]["md5"])
      && md5($pass) === $data["data"][0]["md5"]){
        $check["data"] = $data;
        return $check;
      }

      // パスワード判定
      $data_pass = (isset($data["data"][0]["pass"])) ? $data["data"][0]["pass"] : "";
      if(\mynt::exec('\lib\auth\password','check',array($pass,$data_pass)) === false){
        $check["error"] = 4;
      }
      if($check["error"] === null){
        $check["data"] = $data;
      }
      
    }

    return $check;
  }

  public static function login_check($mail="",$pass=""){
    $check = self::login_error($mail,$pass);

    if($check["error"] === null){
      self::login_success($check["data"]["data"][0]);
      return 1;
    }
    else{
      return 0;
    }
  }

  public static function login_page(){
    $mail = $_POST["mail"];
    $pass = $_POST["pass"];

    // 未入力チェック
    if(self::check_exist($mail,$pass) === false){
      $_POST["message"] = "メールアドレスとパスワードとパスワードを入力してください。";
      return;
    }

    // ログインcheck
    $data = self::load($mail);
// print_r($data);
    // パスワード判定
    $data_pass = (isset($data["data"][0]["pass"])) ? $data["data"][0]["pass"] : "";
    if(!$data || self::check_mail($data) === false
    || \mynt::exec('\lib\auth\password','check',array($pass,$data_pass)) === false){return $pass."/".$data_pass;
      $_POST["message"] = "メールアドレスまたはパスワードが違います。";
      return;
    }

    // login成功
    self::login_success($check["data"]["data"][0]);
    self::redirect_query();
    return;
  }



  public static function logout($url=""){
    \mynt::exec('\lib\auth\session' , "removeSession");
    session_destroy();

    //リダイレクト※URL指定がなければ、ログイン画面へ遷移
    $url = ($url) ? $url : \mynt::exec('\lib\common\url' , "getUrl");

    \mynt::exec('\lib\common\url' , "setUrl" , array($url));
  }

  public static function view($message="",$query_ng=""){
    if(!$url_ng){die("Error : ".$message);}

    $_REQUEST["message"] = $message;
    $query_arr = \mynt::exec('\mynt\common\parse','proper_parse_str',array($query_ng));
    $query_arr["f"] = isset($query_arr["f"]) ? $query_arr["f"] : "";
    $query_arr["p"] = isset($query_arr["p"]) ? $query_arr["p"] : "";
    $f = isset($_GET["f"]) ? $_GET["f"] : $query_arr["f"];
    $p = isset($_GET["p"]) ? $_GET["p"] : $query_arr["p"];

    \mynt::page($f,$p);
    exit();
  }


  // 未入力チェック
  public static function check_exist($mail , $pass){
    if(!$mail || !$pass){
      return false;
    }
    else{
      return true;
    }
  }

  // データload
  public static function load($mail=""){
    if(!$mail){return;}
    $wheres = array("mail" => $mail);
    return \mynt::data_load('' , 'lib_account' , array() , $wheres);
  }

  // mailチェック
  public static function check_mail($res=""){
    if(!$res || $res["status"] === "error"){
      return false;
    }
    else{
      return $res["data"][0];
    }
  }

  // 権限チェック
  public static function check_auth($auth){
    if(!isset($_POST["restrict_auth"]) || !$_POST["restrict_auth"]){return true;}

    if(!$auth){return false;}

    $restrict_auths = explode(",",$_POST["restrict_auth"]);

    if(!in_array($auth , $restrict_auths)){
      return false;
    }
    else{
      return true;
    }
  }

  // ログイン成功処理
  public static function login_success($data){
    $_SESSION["login_id"] = $data["id"];
    $_SESSION["id"]       = $data["id"];
    // $_SESSION["mail"]     = $data["mail"];
    $_SESSION["auth"]     = (isset($data["auth"])) ? $data["auth"] : "";
  }

  // リダイレクト処理
  public static function redirect_query($path=""){
    if(!$path){
      $url = \mynt::exec('\lib\common\url' , "getDir");
    }
    else if(preg_match("/^\?(.+?)$/" , $path , $match)){
      $url = \mynt::exec('\lib\common\url' , "getUrl") ."?". $match[1];
    }
    else if(preg_match("/^(http:\/\/|https:\/\/)(.+?)/" , $path)){
      $url = $path;
    }
    else{
      $url = \mynt::exec('\lib\common\url' , "getUrl");
    }
// die($url);
// $url = \mynt::exec('\lib\common\url' , "getUrl") . ($query ? "?".$query : "");
    \mynt::exec('\lib\common\url' , "setUrl" , array($url));
  }


}