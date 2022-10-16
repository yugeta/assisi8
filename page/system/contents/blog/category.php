<?php
namespace page\system\contents\blog;

class category{

  // 登録データのマスターデータのユニーク値を取得
  public static $lists = null;
  public static function getLists(){
    if(self::$lists === null){
      $res = \mynt::data_load('','lib_blog',array("category"));
      if($res["status"] === "ok"){
        $datas = array();
        foreach($res["data"] as $data){
          if(in_array($data["category"] , $datas)){continue;}
          $datas[] = $data["category"];
        }
        sort($datas);
        self::$lists = $datas;
      }
      else{
        self::$lists = array();
      }
    }
    return self::$lists;
  }

  // categoryマスターデータの読み込み
  public static $db = null;
  public static function getDB($id=""){
    if(self::$db === null){
      $wheres = $id ? array("id" => $id) : array();
      $res = \mynt::data_load("","lib_blog_category",array(),$wheres,array("id"=>"SORT_ASC"));
      if($res["status"] === "ok"){
        self::$db = $res["data"];
      }
      else{
        self::$db = array();
      }
    }
    return self::$db;
  }

  // optionタグ出力
  public static function getTags_option($category=""){
    $lists = self::getAuthLists();
    $html = "";
    // マスター登録有り
    if($lists && count($lists)){
      foreach($lists as $list){
        $selected = ($category == $list["id"]) ? "selected" : "";
        $html .= "<option value='".$list["id"]."' ".$selected.">".$list["name"]."</option>".PHP_EOL;
      }
    }
    return $html;
  }
  public static function getAuthLists(){
    $lists = self::getDB();
    if(!$lists){return;}
    $arr = array();
    foreach($lists as $list){

      if($_SESSION["auth"] != 4 && isset($list["users"]) && $list["users"]){
        $sp = explode(",",$list["users"]);
        if(!in_array($_SESSION["id"] , $sp)){continue;}
      }

      array_push($arr , $list);
    }
    return $arr;
  }

  public static function getLists_tr(){
    $datas = self::getDB();


    foreach($datas as $data){
      $users = self::getUserNames($data["users"]);

      $html .= "<tr data-id='".$data["id"]."'>";
      $html .= "<th class='num'></th>";
      $html .= "<td class='name'>".$data["name"]."</td>";
      $html .= "<td class='users'>".$users."</td>";
      $html .= "<td class='memo'>".$data["memo"]."</td>";
      $html .= "</tr>".PHP_EOL;
    }
    return $html;
  }

  public static function getUserNames($value=""){
    if(!$value){return "全員";}
    $sp = explode(",",$value);

    $lists = \mynt::exec('\page\system\contents\users\users','getLists');
    if(!$lists || !count($lists)){return "全員";}

    $users = array();
    for($i=0; $i<count($sp); $i++){
      $name = "test";
      for($j=0; $j<count($lists); $j++){
        if($lists[$j]["id"] == $sp[$i]){
          $name = isset($lists[$j]["name"]) && $lists[$j]["name"] ? $lists[$j]["name"] : $lists[$j]["mail"];
          break;
        }
      }
      array_push($users , $name);
    }
    return $users ? join(" , ",$users) : "全員";
  }

  public static $cache_category = array();
  public static function getValue($id="" , $key=""){
    if(!$id){return;}
    if(!isset(self::$cache_category[$id])){
      $res = self::getDB($id);
      self::$cache_category[$id] = $res[0];
    }
    if($key && isset(self::$cache_category[$id][$key])){
      return self::$cache_category[$id][$key] ? self::$cache_category[$id][$key] : "";
    }
    else if(!$key && isset(self::$cache_category[$id])){
      return self::$cache_category[$id] ? self::$cache_category[$id] : null;
    }
    else{
      return "";
    }
  }

  public static function post($id=""){
    $data = array(
      "name"  => $_POST["name"],
      "users" => $_POST["users"],
      "memo"  => $_POST["memo"],
      "entry" => date("YmdHis")
    );
    if($id){
      $data["id"] = $id;
    }
    $res = \mynt::data_save('','lib_blog_category' , $data);

    $redirect = isset($_POST["redirect"]) && $_POST["redirect"] ? $_POST["redirect"] : \mynt::exec('\lib\url\common','getUrl');
    \mynt::exec('\lib\url\common' , 'setUrl' , array($redirect));
  }

  public static function getUsers(){
    $lists = \mynt::exec('\page\system\contents\users\users','getLists');
    return json_encode($lists , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  public static function remove($id=""){
    if($id){
      $res = \mynt::data_del('','lib_blog_category',array(),array("id"=>$id));
    }
    else{
      $res = array(
        "status" => "error",
        "message" => "No id value."
      );
    }
    return json_encode($res , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  public static function getDefaultCategory(){
    $category_datas = self::getAuthLists();
    $categorys = array();
    if($category_datas && count($category_datas)){
      foreach($category_datas as $data){
        array_push($categorys , $data["id"]);
      }
    }

    if(isset($_GET["category"]) && $_GET["category"]){
      if(in_array($_GET["category"] , $categorys)){
        return $_GET["category"];
      }
      else{
        return;
      }
    }

    if(count($categorys)){
      return $categorys[0];
    }
    else{
      return;
    }
  }


}