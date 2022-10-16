<?php
namespace lib\page;

class setting{
  public static $dir = "data/";
  public static function getDir(){
    return self::$dir;
  }
  public static $setting_file = "lib_setting.json";
  public static function load($current_page=""){
    // if(!$current_page){
    //   $database_setting = \mynt::exec('\lib\data\database','getSetting');
    //   if(!$database_setting || !isset($database_setting["page"]) || !$database_setting["page"]){return;}
    //   $current_page = $database_setting["page"];
    // }
    // $currentPage = self::getCurrentPage();
    $res = \mynt::data_load('','lib_setting',array("page"));
    if($res["status"] === "ok"){
      return $res["data"];
    }
    else{
      return false;
    }
    // $path = self::$dir . $currentPage ."/". self::$setting_file;
    // if(!is_file($path)){return;}
    // $txt = file_get_contents($path);
    // if(!$txt){return;}
    // $json = json_decode($txt , true);
    // return $json;
  }
  public static function getCurrentPage(){
    $res = \mynt::data_load('','lib_setting',array("page"));
    return $res["status"] === "ok" ? $res["data"]["page"] : "";
  }

  // $GLOBALS["page"]にpage/***/setting.jsonデータを格納する。
  public static function load_global(){
    $GLOBALS["page"] = self::load();
    $GLOBALS["database"] = \mynt::exec('\lib\data\database','getSetting');
  }

  public static function setGlobals(){
    $database_setting = \mynt::exec('\lib\data\database','getSetting');
    $GLOBALS["config"] = array(
      "page" => self::getCurrentPage(),
      "data" => $database_setting["database"]
    );
    // $GLOBALS["setting"] = $database_setting;
    $setting = \mynt::data_load('','lib_setting',array("page"));
    if($setting["status"] === "ok"){
      $GLOBALS["setting"] = $setting["data"];
    }
  }


  public static function lists_options(){
    $lists = scandir(self::$dir);
    $html = array();
    $db = \mynt::data_load('','lib_setting',array("page"));
    $currentPage = ($db["status"] === "ok") ? $db["data"]["page"] : "";
    for($i=0; $i<count($lists); $i++){
      if(!$lists[$i]){continue;}
      if($lists[$i] === "." || $lists[$i] === ".."){continue;}
      if(!is_dir(self::$dir.$lists[$i])){continue;}
      if(!is_file(self::$dir.$lists[$i]."/lib_setting.json")){continue;}
      $pageInfo = self::getPageInfo($lists[$i]);
      $selected = $lists[$i] === $currentPage ? "selected" : "";
      $sel_val  = $lists[$i] === $currentPage ? " *" : "";
      $name = isset($pageInfo["name"]) && $pageInfo["name"] ? $pageInfo["name"] : $lists[$i];
      $html .= "<option value='".$lists[$i]."' ".$selected.">".$name.$sel_val."</option>".PHP_EOL;
    }
    return $html;
  }
  public static function getPageInfo($page_id){
    $path = self::$dir . $page_id ."/setting.json";
    if(!is_file($path)){return null;}
    $text = file_get_contents($path);
    $json = json_decode($text , true);
    // print_r($json);
    return $json;
  }


  public static function save_new(){

  }
  public static function save_edit(){

  }
  public static function save_change(){
    $current = \mynt::data_load('','lib_setting');
    $data = $current["status"] === "ok" ? $current["data"] : array();
    $data["page"]  = $_POST["setting"]["page"];
    $data["entry"] = date("YmdHis");
    
    $res = \mynt::data_save('','lib_setting',$data);
    if($res["status"] === "ok"){
      self::redirect();
    }
    else{
      die("Error ! ".$res["message"]);
    }
  }

  public static function redirect(){
    if(isset($_POST["redirect"])){
      \mynt::exec('\lib\url\common','setUrl',array($_POST["redirect"]));
    }
    else{
      $url = \mynt::exec('\lib\url\common','getUrl');
      \mynt::exec('\lib\url\common','setUrl',array($url));
    }
  }
}
