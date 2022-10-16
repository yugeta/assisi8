<?php
namespace page\system\contents\account;

class data{
  // cache登録されたデータの取得
  public static function getHashValue($hash , $mode , $key){
    $wheres = array("hash" => $hash);
    $sort   = array("entry" => "SORT_ASC");
    $res = \mynt::data_load("" , "lib_account_entry_cache" , array() , $wheres , $sort);
    if($res["status"] === "error"){
      die("Error ! : 正常に処理できません。システム管理者までお問い合わください。");
    }
    $keys = array_keys($res["data"]);
    $data = $res["data"][$keys[count($keys)-1]];

    if(isset($data[$key])){
      return $data[$key];
    }
    else{
      return "";
    }
  }

  // account-data
  public static function getAccount($uid="" , $key=""){
    if(!$key || !$uid){return;}
    $res = \mynt::data_load("","lib_account",array(),array("id"=>$uid));
    if($res["status"] === "ok" && isset($res["data"][0][$key])){
      return $res["data"][0][$key];
    }
    else{
      return;
    }
  }

  // property-data
  public static function getProperry($uid="" , $key=""){
    // if(!$key || !$uid){return;}
    // $res = \mynt::data_load("","lib_property",array(),array("id"=>$uid));
    // if($res["status"] === "ok" && isset($res["data"][0][$key])){
    //   return $res["data"][0][$key];
    // }
    // else{
    //   return;
    // }
    return self::getProperty($uid,$key);
  }
  public static function getProperty($uid="" , $key=""){
    if(!$key || !$uid){return;}
    $res = \mynt::data_load("","lib_property",array(),array("id"=>$uid));
    if($res["status"] === "ok" && isset($res["data"][0][$key])){
      return $res["data"][0][$key];
    }
    else{
      return;
    }
  }
}