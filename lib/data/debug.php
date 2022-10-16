<?php
namespace lib\data;

/**
 * Howto
 * 
 * # 簡易チェック
 * $ php debug.php -m test-1 -p lib/data/debug.php -c \\lib\\data\\debug -f auto
 * 
 * # 詳細表示
 * $ php debug.php -m test-1 -p lib/data/debug.php -c \\lib\\data\\debug -f auto -a view
 */

class debug{

  // data.php
  public static function auto($mode=""){
    self::database_getSetting($mode);
    self::database_getTables($mode);
    if(is_file("data/database.json")){
      self::database_save($mode);
      $id = self::database_load($mode);
      self::database_del($mode,$id);
    }
    else{
      echo "-- Error : no file. (data/database.json)".PHP_EOL;
    }
    
  }

  // database.php ----------

  // db基本設定の読み込み
  public static function database_getSetting($mode=""){
    $res = \mynt::exec('\lib\data\database',"getSetting",array());
    
    echo "-- database_getSetting : ";
    if($mode==="view"){
      echo PHP_EOL;
      echo json_encode($res , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL;
    }
    else{
      if($res){
        echo 'OK';
      }
      else{
        echo "NG";
      }
    }
    echo PHP_EOL;
  }

  // tables設定の読み込み(lib内のみ)
  public static function database_getTables($mode=""){
    $files = scandir("lib/data/tables/");
    $ng_flg = 0;
    for($i=0; $i<count($files); $i++){
      if(!preg_match("/^(.+?)\.json$/",$files[$i] , $match)){continue;}
      $table_name = $match[1];
      $res = \mynt::exec('\lib\data\database',"getTableSetting",array($table_name));
      if($mode==="view"){
        echo "-- database_getTables : " . "lib/data/tables/" . $files[$i] . " : ";
        echo PHP_EOL;
        echo json_encode($res , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo PHP_EOL;
      }
      else{
        if(!$res){
          $ng_flg++;
        }
      }
    }
    if(!$mode){
      echo "-- database_getTables : ";
      if($ng_flg === 0){
        echo 'OK'.PHP_EOL;
      }
      else{
        echo 'NG'.PHP_EOL;
      }
    }
  }

  // 
  public static function database_save($mode=""){
    $datas = array(
      "test" => "A",
      "key"   => "test-key",
      "value" => "test-value",
      "entry" => date("YmdHis")
    );
    $wheres = array();
    $res = \mynt::data_save("","debug",$datas,$wheres);

    echo "-- database_save : ";
    if($res["status"] === "ok"){
      echo "OK".PHP_EOL;
    }
    else{
      echo "NG".PHP_EOL;
      echo json_encode($res , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL;
    }
  }

  public static function database_load($mode=""){
    $keys = array();
    $wheres = array(
      "test" => "A",
      "key"   => "test-key"
    );
    $res = \mynt::data_load("","debug",$keys,$wheres);
    $id = isset($res["data"]) && count($res["data"]) ? $res["data"][0]["id"] : "";
    echo "-- database_load : [id . ".$id."] : ";
    if($res["status"] === "ok"){
      echo "OK".PHP_EOL;
      // print_r($res);
    }
    else{
      echo "NG".PHP_EOL;
      echo json_encode($res , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL;
    }
    return $id;
  }

  public static function database_del($mode="",$id){
    $datas = array();
    $wheres = array(
      "test" => "A",
      "id"   => $id
    );
    $res = \mynt::data_del("","debug",$datas,$wheres);

    echo "-- database_del : ";
    if($res["status"] === "ok"){
      echo "OK".PHP_EOL;
    }
    else{
      echo "NG".PHP_EOL;
      echo json_encode($res , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL;
    }
  }

}