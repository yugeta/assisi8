<?php
namespace lib\data;
/**
 * Path    : lib/php/data_json.php
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.1.0
 * Summary : web-apiをデータベースとして利用（API先もmyntstudio）
 * Example : 
 */

class data_net{

  // まとめ処理
  public static function data_save($dbname="",$table="",$datas=array(),$wheres=array()){

  }


  // check-init : アクセス先の確認
  public static function checkInit($config=""){
    if(!$config || !isset($config["addr"])){
      die("Error (code:init-001) No Config.");
    }
    // アクセスチェック
// die("a");
// echo "http://".$config["addr"]."net.php";
    return file_get_contents($config["addr"]);
  }



  // database-webApi先へ送信

  // public static function get_table($table=""){
    
  // }

  public static function table_create($table=""){
    
  }

  public static function data_select($table="",$keys=array(),$values=array()){

  }
  public static function data_selects($table="",$keys=array(),$values=array()){

  }

  public static function data_update($table="",$keys=array(),$values=array()){
    
  }

  public static function data_delete($table="",$keys=array(),$values=array()){
    
  }


}