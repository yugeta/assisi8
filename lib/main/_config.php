<?php
namespace lib\main;

class config{
  // public static function get($key=""){
  //   // die( method_exists("\mynt","execu") );
  //   // die("config");
    
  //   $config = self::loadConfig();
  //   if(isset($config[$key])){
  //     return $config[$key];
  //   }
  //   // デフォルト
  //   else{
  //     return $config;
  //   }
    
  // }


  // data環境設定
  public static $config_data = null;
  public static function get(){
    if(self::$config_data !== null){return self::$config_data;}
    $path = "lib/data/database.json";
    if(!is_file($path)){return;}
    $text = file_get_contents($path);
    $config = json_decode($text , true);

    $dbname = (isset($config["database"])) ? $config["database"] : "";
    $path2 = "mynt/config/tables/".$dbname.".json";
    if($dbname && is_file($path2)){
      $db2 = json_decode(file_get_contents($path2) , true);
      if($db2){
        $config["tables"] = array_merge($config["tables"] , $db2);
      }
    }


    self::$config_data = $config;
    return self::$config_data;
  }




  // public static $path_config = "data/config.json";
  // public static $data_config = null;
  // public static function loadConfig(){
  //   if(!is_file(self::$path_config)){return null;}
  //   if(self::$data_config === null){
  //     $config = file_get_contents(self::$path_config);
  //     if(!$config){return null;}
  //     self::$data_config = json_decode($config , true);
  //   }
  //   return self::$data_config;
  // }


}