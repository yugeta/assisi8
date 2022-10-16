<?php
namespace lib\data;

class common{



  // indexkeyの取得
  public static $indexKeys = array();
  public static function getIndexKeys($table_name=""){
    if(!$table_name){return null;}
    if(!isset(self::$indexKeys[$table_name])){
      $dir = \mynt::exec("\\lib\\data\\database" , "getDir");
      if(!$dir || !is_dir($dir)){
        die("Error (code:json-002) Config-error.");
      }

      $tables = \mynt::exec('\lib\data\database' , "getTableSetting" , array($table_name));
      if(!$tables || !isset($tables["columns"])){return;}

      $keys = array();
      foreach($tables["columns"] as $key => $val){
        // if(!isset($val["index"])){continue;}
        if(!isset($val["index"]) || !(string)$val["index"]){continue;}
        if(!isset($keys[$val["index"]])){
          $keys[$val["index"]] = array();
        }
        $keys[$val["index"]][] = $key;
      }
      self::$indexKeys[$table_name] = $keys;
    }
    return self::$indexKeys[$table_name];
  }

  public static $tableColumns_defaultStrings = array();
  public static function getTableColumns_defaultString($table_name="",$column_name=""){
    if($table_name==="" || $column_name===""){return;}
    if(!isset(self::$tableColumns_defaultStrings[$table_name])
    || !isset(self::$tableColumns_defaultStrings[$table_name][$column_name])){
      $tables = \mynt::exec('\lib\data\database' , "getTableSetting" , array($table_name));
      if($tables
      && isset($tables["columns"])
      && isset($tables["columns"][$column_name])
      && isset($tables["columns"][$column_name]["default"])){
        self::$tableColumns_defaultStrings[$table_name][$column_name] = $tables["columns"][$column_name]["default"];
      }
      else{
        self::$tableColumns_defaultStrings[$table_name][$column_name] = "";
      }
    }
    return self::$tableColumns_defaultStrings[$table_name][$column_name];
  }


}