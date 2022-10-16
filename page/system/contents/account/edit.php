<?php
namespace page\system\contents\account;

class edit{

  // アカウント編集
  public static function post(){

    // クエリチェック
    if(!isset($_POST["account_id"]) || !$_POST["account_id"]
    || !isset($_POST["mail"])
    || !isset($_POST["pass"])
    || !isset($_POST["pass2"])
    || !isset($_POST["name"])
    || !isset($_POST["memo"])){
      $_POST["error"] = "System error !!!";
      return false;
    }

    // 未入力check : 必至項目のみ
    if(!$_POST["mail"]){
      $_POST["error"] = "未入力の項目があります。<br>戻るボタンを押して再入力してください。";
      return false;
    }

    // パスワード処理
    if($_POST["pass"] && $_POST["pass2"] && $_POST["pass"] !== $_POST["pass2"]){
      $_POST["error"] = "パスワードの登録が間違っています。";
      return false;
    }
    else if(($_POST["pass"] && !$_POST["pass2"])
    || (!$_POST["pass"] && $_POST["pass2"])){
      $_POST["error"] = "パスワードの登録に未入力箇所がありません。";
      return false;
    }

    // 登録済みメールアドレスの確認
    $currentData = \mynt::data_load("","lib_account",array(),array("mail"=>$_POST["mail"]));
    if($currentData["status"] === "ok" && $currentData["data"][0]["id"] != $_POST["account_id"]){
      $_POST["error"] = "すでに使用されているメールアドレスです。";
      return false;
    }
    else if($currentData["status"] === "error"){
      $_POST["error"] = "System Error !! (code:d-001)";
      return false;
    }

    // パスワードに変更がない場合は、データベースから取得する
    $pass = "";
    if($_POST["pass"] && $_POST["pass2"] && $_POST["pass"] === $_POST["pass2"]){
      $pass = \mynt::exec('\lib\auth\password','encode',array($_POST["pass"]));
    }
    else if(!$_POST["pass"] && !$_POST["pass2"]){
      $pass = $currentData["data"][0]["pass"];
    }
    if(!$pass){
      $_POST["error"] = "Error ! (code : p-001)";
      return false;
    }


    $entry = date("YmdHis");
    $auth  = isset($currentData["data"][0]["auth"]) ? $currentData["data"][0]["auth"] : "";
    $memo  = isset($currentData["data"][0]["memo"]) ? $currentData["data"][0]["memo"] : "";

    // データ登録 (account)
    if($currentData["data"][0]["mail"] !== $_POST["mail"]
    || $currentData["data"][0]["pass"] !== $pass){
      $data_account = array(
        "id"    => $_POST["account_id"],
        "auth"  => $auth,
        "mail"  => $_POST["mail"],
        "pass"  => $pass,
        "entry" => $entry
      );
      $where_account = array(
        "id"  => $_POST["account_id"]
      );
      $res_account = \mynt::data_save("","lib_account",$data_account,$where_account);
    }
    

    // データ登録 (property)
    $currentProperty = \mynt::data_load("","lib_property",array(),array("id"=>$_POST["account_id"]));
    if(($currentProperty["status"] === "error" && $_POST["name"])
    || ($currentProperty["status"] === "error" && $_POST["memo"])
    || ($currentProperty["status"] === "ok" && $currentProperty["data"][0]["name"] !== $_POST["name"])
    || ($currentProperty["status"] === "ok" && $currentProperty["data"][0]["memo"] !== $_POST["memo"])){
      $data_property = array(
        "id"    => $_POST["account_id"],
        "name"  => $_POST["name"],
        "memo"  => $memo,
        "entry" => $entry
      );
      $where_property = array(
        "id"  => $_POST["account_id"]
      );
      $res_property = \mynt::data_save("","lib_property",$data_property,$where_property);
    }

    $_POST["success"] = "正常に登録できました。";
    return false;


    // // リダイレクト処理
    // if($res_account["status"]  === "error"
    // || $res_property["status"] === "error"){
    //   $_POST["error"] = "Error! データが正常に登録できませんでした。";
    //   return;
    // }

    // if(isset($_POST["redirect"]) && $_POST["redirect"]){
    //   \mynt::exec('lib\common\url',"setUrl",array($_POST["redirect"]));
    // }
    // else{
    //   $url = \mynt::exec('\lib\url\common',"getUrl");
    //   \mynt::exec('lib\common\url',"setUrl",array($url));
    // }
  }

}