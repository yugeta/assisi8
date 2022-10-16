<?php
namespace lib\data;

class database{

  public static $dir = "data/";
  public static function getDir(){
    return self::$dir;
  }

  // database設定の取得
  public static $setting_file = "data/database.json";
  public static $setting = null;
  public static function getSettingFile(){
    return self::$setting_file;
  }
  // $mode @ 強制上書き [ "overwrite" ... ]
  public static function getSetting($mode=""){
    if(!is_file(self::$setting_file)){return;}
    if(self::$setting === null || $mode !== ""){
      // $txt = self::load();
      $txt = file_get_contents(self::$setting_file);
      self::$setting = json_decode($txt , true);
    }
    return self::$setting;
  }
  public static function getSetting_clear(){
    if(self::$setting !== null){
      self::$setting = null;
      return true;
    }
    else{
      return false;
    }
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

    $res = isset(self::$table_paths[$table_name]) ? self::$table_paths[$table_name] : null;
    return $res;
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

  // data/setting.jsonの保存処理
  public static function save($datas=array()){
    if(!$datas){return false;}

    // make-dir
    $dir = \mynt::exec('\lib\data\database',"getDir",array());
    if(!is_dir($dir)){
      mkdir($dir , 0777 , true);
    }

    $datas["entry"] = date("YmdHis");
    $dir  = self::$dir;
    $datas["session_path"] = $dir."session/";
    $path = self::$setting_file;
    $json = json_encode($datas , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    file_put_contents($path , $json);

    if(is_file($path)){
      return true;
    }
    else{
      return false;
    }
  }

  // public static $load = null;
  // public static function load(){
  //   if(is_file(self::$setting_file) && self::$load === null){
  //     self::$load =  file_get_contents(self::$setting_file);
  //   }
  //   return self::$load;
  // }

  // 登録されているtable一覧を取得（select/optionタグで返す）
  public static function getTables_option($value=""){

    $html = "";

    // system
    $system_dir = "lib/data/tables/";
    if(is_dir($system_dir)){
      $files = scandir($system_dir);
      for($i=0; $i<count($files); $i++){
        if(preg_match("/^(.+?)\.json$/" , $files[$i] , $match)){
          $tableSetting = self::getTableSetting($match[1]);
          $selected = ($value && $value === $match[1]) ? "selected" : "";
          $html .= "<option value='".$match[1]."' ".$selected.">lib : ".$tableSetting["info"]["name"]." (".$match[1].")"."</option>".PHP_EOL;
        }
      }
    }

    // service
    $setting = \mynt::exec('\lib\data\database','getSetting');
    $service_dir = "data/".$setting["database"]."/tables/";
    if(is_dir($service_dir)){
      $files = scandir($service_dir);
      for($i=0; $i<count($files); $i++){
        if(preg_match("/^(.+?)\.json$/" , $files[$i] , $match)){
          $tableSetting = self::getTableSetting($match[1]);
          $selected = ($value && $value === $match[1]) ? "selected" : "";
          $html .= "<option value='".$match[1]."' ".$selected.">service : ".$tableSetting["info"]["name"]." (".$match[1].")"."</option>".PHP_EOL;
        }
      }
    }

    return $html;
  }
  

}