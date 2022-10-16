<?php
namespace lib\data;
/**
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.1.0
 * Summary : データの保存、削除、更新などの処理をテキストファイル、mysql、web-apiなどに対応する
 * Example : 
 */

class data{

  public static function getType(){
    $setting = \mynt::exec("\\lib\\data\\database" , "getSetting" , array());
    if(!$setting
    || !isset($setting["type"])
    || !$setting["type"]){return;}
    return strtolower($setting["type"]);
  }

  public static function save(){
    $q = func_get_args();
    $q = \mynt::exec('\lib\data\replace','crlf2lf',array($q));
    switch(self::getType()){
      case "mysql"  : return \mynt::exec('\lib\data\data_mysql'  , "data_save" , $q);
      case "net"    : return \mynt::exec('\lib\data\data_net'    , "data_save" , $q);
      case "json"   : return \mynt::exec('\lib\data\data_json'   , "data_save" , $q);
      case "sqlite" : return \mynt::exec('\lib\data\data_sqlite' , "data_save" , $q);
    }
    return null;
  }
  public static function load($dbname="",$table="",$keys=array(),$wheres=array(),$sort=array()){
    $q = func_get_args();
    switch(self::getType()){
      case "mysql"  : return \mynt::exec('\lib\data\data_mysql'  , "data_load" , $q);
      case "net"    : return \mynt::exec('\lib\data\data_net'    , "data_load" , $q);
      // case "json"   : return \mynt::exec('\lib\data\data_json'   , "data_load" , array($dbname,$table,$keys,$wheres,$sort));
      case "json"   : return \mynt::exec('\lib\data\data_json'   , "data_load" , $q);
      case "sqlite" : return \mynt::exec('\lib\data\data_sqlite' , "data_load" , $q);
    }
    return null;
  }
  public static function del(){
    $q = func_get_args();
    switch(self::getType()){
      case "mysql"  : return \mynt::exec('\lib\data\data_mysql'  , "data_del" , $q);
      case "net"    : return \mynt::exec('\lib\data\data_net'    , "data_del" , $q);
      case "json"   : return \mynt::exec('\lib\data\data_json'   , "data_del" , $q);
      case "sqlite" : return \mynt::exec('\lib\data\data_sqlite' , "data_del" , $q);
    }
    return null;
  }





  // public static $dir_config = "mynt/";

  // public static $column = null;
  // public static function column(){
  //   if(self::$column !== null){return array("status"=>"ok","data"=>self::$column);}
  //   //設定データ読込
  //   $setting_path = "mynt/config/tables.json";
  //   if(!is_file($setting_path)){
  //     return array("status"=>"error","message"=>"設定ファイルがありません。","code"=>"sql-035");
  //   }
  //   $table_config = json_decode(file_get_contents($setting_path),true);
  //   if(!$table_config){
  //     return array("status"=>"error","message"=>"設定データがありません。","code"=>"sql-034");
  //   }
  //   self::$column = $table_config;
  //   return array("status"=>"ok","data"=>self::$column);
  // }

  // public static $columns = null;
  // public static function columns(){
  //   if(self::$columns !== null){return array("status"=>"ok","data"=>self::$columns);}

  //   //設定データ読込
  //   $setting_path = "mynt/config/tables.json";
  //   if(!is_file($setting_path)){
  //     return array("status"=>"error","message"=>"設定ファイルがありません。","code"=>"sql-035");
  //   }

  //   $datas = json_decode(file_get_contents($setting_path),true);
  //   if(!$datas){
  //     return array("status"=>"error","message"=>"設定データがありません。","code"=>"sql-034");
  //   }
  //   $table_config = array();
  //   // tables
  //   foreach($datas as $table => $arr){
  //     if(!isset($table_config[$table])){
  //       $table_config[$table] = array();
  //     }
  //     for($i=0; $i<count($arr); $i++){
  //       $table_config[$table][$arr[$i]["name"]] = $arr[$i];
  //     }
  //   }
  //   self::$columns = $table_config;
  //   return array("status"=>"ok","data"=>self::$columns);
  // }

  

  

  public static function checkType(){
    $data = false;
    switch(self::getType()){
      case "mysql":
        $data = self::checkLogin_mysql($id,$pw);
        break;
      case "postgres":
        $data = self::checkLogin_postgres($id,$pw);
        break;
      case "mongodb":
        $data = self::checkLogin_mongodb($id,$pw);
        break;
      case "couchdb":
        $data = self::checkLogin_couchdb($id,$pw);
        break;
      case "sqlite":
        $data = self::checkLogin_sqlite($id,$pw);
        break;
      case "json" :
        $data = self::checkLogin_file($id,$pw);
        break;
    }
		return $data;
  }



  
  
  

  public static function data_load(){
    $q = func_get_args();
    $res = self::load($q[0],$q[1],$q[2],$q[3],$q[4]);

    if(isset($res["status"]) && $res["status"] === "ok"){
      return $res["data"];
    }
    else if(isset($res["status"]) && $res["status"] === "error"){
      return array();
    }
    else{
      return null;
    }
  }

  

  public static function getTableLists($dbname=""){
    switch(self::getType()){
      case "mysql" : return \mynt::exec("\\lib\\data\\data_mysql","getTableLists",array($dbname));break;
      case "net"   : return \mynt::exec("\\lib\\data\\data_net"  ,"getTableLists",array($dbname));break;
      case "json"  : return \mynt::exec("\\lib\\data\\data_json" ,"getTableLists",array($dbname));break;
    }
    return null;
  }

  // public static function getTableIndexLists($dbname="",$tableName=""){
  //   switch(self::getType()){
  //     case "mysql" : return \mynt::exec("\\lib\\data\\data_mysql","getTableIndexLists",array($dbname,$tableName));break;
  //     case "net"   : return \mynt::exec("\\lib\\data\\data_net"  ,"getTableIndexLists",array($dbname,$tableName));break;
  //     case "json"  : return \mynt::exec("\\lib\\data\\data_json" ,"getTableIndexLists",array($dbname,$tableName));break;
  //   }
  //   return null;
  // }

  



  public static function convertValueType($dbname="",$table_name="",$datas=array()){
    $config = \mynt::exec("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    if(!isset($config["tables"][$table_name])){return $datas;}
    foreach($datas as $key => $val){
      if(!isset($config["tables"][$table_name]["columns"][$key])){continue;}
      switch(strtolower($config["tables"][$table_name]["columns"][$key]["type"])){
        case "int":
          $datas[$key] = (int)$val;
          break;

        case "varchar":
          $datas[$key] = (string)$val;
          break;

        case "text":
          $datas[$key] = (string)$val;
          break;

      }
    }
    return $datas;
  }


  public static function get_table_index_lists($dbname="",$tableName=""){
    switch(self::getType()){
      case "mysql" : return "";
      case "net"   : return "";
      case "json"  : return \mynt::exec("\\lib\\data\\data_json" ,"get_index_values",array($dbname , $tableName));
      default      : null;
    }
  }


}