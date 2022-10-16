<?php
namespace lib\design;

class modules{
  public static function load($type="",$indent=""){
    if(!$type){return "";}
    
    $modules = self::getModules($type);
    if(!$modules){return;}

    $html = "";
    foreach($modules as $key => $lists){
      switch($key){
        case "css":
          $html .= self::setCssTags($lists,$indent);
          break;
        case "js":
          $html .= self::setJsTags($lists,$indent);
          break;
      }
    }
    return $html;
  }

  public static function setCssTags($lists,$indent){
    if(!$lists){return "";}
    $add_query_version = isset($GLOBALS["page"]["version"]) ? "?".$GLOBALS["page"]["version"] : "";
    $html = "";
    for($i=0; $i<count($lists); $i++){
      $html .= $indent."<link rel='stylesheet' href='".$lists[$i].$add_query_version."' />".PHP_EOL;
    }
    return $html;
  }
  public static function setJsTags($lists,$indent){
    if(!$lists){return "";}
    $add_query_version = isset($GLOBALS["page"]["version"]) ? "?".$GLOBALS["page"]["version"] : "";
    $html = "";
    for($i=0; $i<count($lists); $i++){
      $html .= $indent."<script src='".$lists[$i].$add_query_version."'></script>".PHP_EOL;
    }
    return $html;
  }

  public static function getModules($type){
    if(!$type){return;}

    $design  = self::get_designName();

    if(!$design){return;}

    $dir = "design/". $design ."/". $type ."/";
    if(!is_dir($dir)){return;}

    $files = scandir($dir);

    $arr = array(
      "css"  => array(),
      "js"   => array()
    );
    for($i=0; $i<count($files); $i++){
      if(preg_match("/^(.+?)\.([a-z]+?)$/i",$files[$i],$match)){
        switch($match[2]){
          case "css":
            $arr["css"][] = $dir.$files[$i];
            break;
          case "js":
            $arr["js"][] = $dir.$files[$i];
            break;
        }
      }
    }
    return $arr;
  }

  public static function get_pageName(){
    if(isset($_GET["p"]) && $_GET["p"]){
      return $_GET["p"];
    }
    else{
      return \mynt::exec('\lib\html\page','getPage');
    }
  }

  public static function get_designName(){

    $page = self::get_pageName();

    if($page === "system"){
      $path = ($page === "system") ? "page/".$page."/setting.json" : "data/".$page."/lib_setting.json";
      if(!is_file($path)){return;}
      $txt = file_get_contents($path);
      $json = json_decode($txt , true);
      if(!$json || !isset($json["design"])){return;}
    }
    else{
      $res = \mynt::data_load('','lib_setting');
      if($res["status"] === "ok" && isset($res["data"]["design"])){
        return $res["data"]["design"];
      }
      else{
        return "";
      }
    }

    
//     
// // die($page);
//     if(!$page){return;}

//     
    
    return $json["design"];
  }
}