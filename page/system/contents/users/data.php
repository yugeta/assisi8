<?php
namespace page\system\contents\users;

class data{

  // 対象authのユーザー一覧を取得
  public static function getAuthUsers($auth_arr=array()){
    if(!$auth_arr){return;}
    $users = array();
    foreach($auth_arr as $num => $auth_key){
      $res = \mynt::data_load('','lib_account',array() , array("auth" => $auth_key));
      if($res["status"] === "ok"){
        foreach($res["data"] as $data){
          array_push($users , $data["id"]);
        }
      }
    }
    return $users;
  }


}