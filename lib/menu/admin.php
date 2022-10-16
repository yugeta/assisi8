<?php
namespace lib\menu;

class admin{
  public static function list_users($service="" , $auth=""){
    if(!$service || !$auth){return;}
    $current_id = $_SESSION["id"];

    // システム管理者
    if($auth == 4){
      $res = \mynt::data_load($service,'lib_account' , [] , [] , ["id"=>"SORT_ASC"]);
    }
    else if($auth == 3){
      $res = array("status" === "error");
      // $res = \mynt::data_load($service,'lib_account' , [] , [] , ["id"=>"SORT_ASC"]);
    }
    else if($auth == 2){
      $res = \mynt::data_load($service,'lib_account' , [] , [] , ["id"=>"SORT_ASC"]);
    }
    else{
      $res = array("status" === "error");
    }

    if($res["status"] === "ok" && $res["data"]){
      $html = "<select class='admin-user-change'>";
      foreach($res["data"] as $data){
        $selected = ($data["id"] == $_SESSION["id"]) ? "selected" : "";
        $name = $data["id"] == $_SESSION["login_id"] ? $data["mail"]." (login)" : $data["mail"];
        $html .= "<option value='".$data["id"]."' ".$selected.">". $name ."</option>";
      }
      $html .= "</select>";
      return $html;
    }
  }
  public static function change_user($id=""){
    if(!isset($_SESSION) || !isset($_SESSION["auth"])){return;}
    // 管理者チェック
    if($_SESSION["auth"] != 4){return;}
    $id = $id ? $id : $_SESSION["login_id"];
    $_SESSION["id"] = $id;
    return true;
    // $uri = \mynt::exec('\lib\url\common','getUri');
    // \mynt::exec('\lib\url\common','setUrl' , array($uri));
  }
}