<?php
namespace lib\data;

class table{
  // table設定データの一覧取得
  public static function getLists(){
    $lists_lib  = self::getLists_lib();
    $lists_data = self::getLists_data();
    $lists = array_merge($lists_lib , $lists_data);
    // $json  = json_encode($lists);
    return $lists;
  }

  // table設定データ(system領域)
  public static function getLists_lib(){
    $path = self::$tableLibDir;
    if(!$path || !is_dir($path)){return array();}
    return array_diff(scandir($path) , array(".",".."));
  }
  // table設定データ(user領域)
  public static function getLists_data(){
    $database_setting = \mynt::exec('\lib\data\database','getSetting');
    if(!$database_setting || !isset($database_setting["database"]) || !$database_setting["database"]){return array();}
    $path = \mynt::exec('\lib\data\database','getDir') . $database_setting["database"] ."/tables/";
    if(!is_dir($path)){return array();}
    return array_diff(scandir($path) , array(".",".."));
  }





  // table設定ファイルの検索
  public static $tableLibDir = "lib/data/tables/";
  public static $table_paths = array();
  public static function searchTableSettingPath($table_name){
    if(!$table_name){return;}
    
    // data
    if(!isset(self::$table_paths[$table_name])){
      $setting = self::getSetting();
      if(!$setting){return;}
      $path_lib = self::$dir . $setting["database"] ."/tables/". $table_name .".json";
      if(is_file($path_lib)){
        self::$table_paths[$table_name] = $path_lib;
      }
    }

    // lib
    if(!isset(self::$table_paths[$table_name])){
      $path_lib = self::$tableLibDir . $table_name .".json";
      if(is_file($path_lib)){
        self::$table_paths[$table_name] = $path_lib;
      }
    }

    return self::$table_paths[$table_name];
  }


  // tableデータ格納変数
  public static $tables = array();

  // 任意テーブル設定の取得（基本）
  public static function getTableSetting($table_name=""){
    if(!$table_name){return;}
    $setting = self::getSetting();
    if(!isset(self::$tables[$table_name])){
      $path = self::searchTableSettingPath($table_name);
      if(!is_file($path)){return;}
      $txt = file_get_contents($path);
      self::$tables[$table_name] = json_decode($txt,true);
    }
    return self::$tables[$table_name];
  }
}