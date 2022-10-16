<?php
namespace lib\install;

class value{

  public static function setValue($key="" , $default=""){
    if(!$key){return $default;}

    $database_json_path = \mynt::exec('\lib\data\database','getSettingFile');
    if($database_json_path && is_file($database_json_path)){
      $setting = \mynt::exec('\lib\data\database','getSetting');
      if(isset($setting[$key])){
        return $setting[$key];
      }
    }
    return $default;
  }
  // public static $type_options = array("json" , "mysql" , "net" , "sqlite" , "csv");

  public static function getOptionLists($type=""){
    if(!$type){return array();}
    $path = "lib/install/datas/".$type.".json";
    if(!is_file($path)){return array();}
    $txt = file_get_contents($path);
    return json_decode($txt , true);
  }

  public static function setOptions_type($type=""){
    if(!$type){return "";}
    $html = "";
    $datas = self::getOptionLists($type);
    foreach($datas as $num => $option){
      if(isset($option["flg"]) && $option["flg"] == 1){continue;}
      $html .= "<option value='".$option["type"]."'>".$option["name"]."</option>";
      $html .= PHP_EOL;
    }
    return $html;
  }


}