<?php
namespace lib\data;

class data_common{
  public static function getTableIndexLists($dbname="",$tableName=""){
    $type   = \mynt::exec('\lib\data\data',"getType");
    switch($type){
      case "mysql" : return "";
      case "net"   : return "";
      case "json"  : return \mynt::exec("\\lib\\data\\data_json" ,"get_index_values",array($dbname , $tableName));
      default      : null;
    }
  }
}