<?php
namespace lib\data;
/**
 * Path    : lib/php/data_mysql.php
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.1.0
 * Summary : mysqlをデータベースとして利用
 * Example : 
 */

class data_mysql{

  // まとめ処理
  public static function data_save($dbname="",$table="",$datas=array(),$wheres=array()){

    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);

    if(!$db){
      $check  = self::check_database();
      if($check["status"] === "error"){
        return $check;
      }
      $db = $check["database"];
    }
    
    $check = self::check_table($dbname,$table);
    if($check["status"] === "error"){
      if(isset($config["tables"][$table])){
        $res = self::create_table("",$table);
        if($res["status"] === "error"){
          return $res;
        }
      }
      else{
        return $check;
      }
    }

    // whereが無い場合は追記
    if(!$wheres){
      return self::data_insert($dbname,$table,$datas);
    }

    // 追記か更新の判定
    $check = self::data_select($dbname,$table,array(),$wheres);
    $db->close();
    if($check["status"] === "error"){
      return $check;
    }

    if(!count($check["data"])){
      return self::data_insert($dbname,$table,$datas);
    }
    else{
      return self::data_update($dbname,$table,$datas,$wheres);
    }
  }





  

  // // account-data
  // public static function getMail2Data($mail=""){
  //   $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
  //   $mysql = new \mysqli($config["host"] , $config["user"] , $config["pass"] , $config["database"]);
  //   $sql = "SELECT id,mail,auth,md5,entry FROM account WHERE mail = '".$mail."'";
  //   $ret = $mysql->query($sql);
  //   $data = null;
  //   while($row = mysqli_fetch_assoc($ret)){
  //     $data = $row;
  //     break;
  //   }
  //   return $data;
  // }


  public static function get_database(){
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $db = new \mysqli($config["host"] , $config["user"] , $config["pass"] , $config["database"]);
    if($db->connect_error){
      die("Error (code:init-001) No Config.");
    }
    return $db;
  }


  public static function table_create($table="" , $keys=array()){
    $db = self::get_database();
    $db->query("CREATE TABLE ".$table."  (".implode(",",$keys).") charset=utf8");
    $db->close();
  }
  public static function table_exists($table=""){
    $db = self::get_database();
    $res = $db->query("SELECT 1 FROM ".$table." LIMIT 1");
    $db->close();
    if($res){
      return true;
    }
    else{
      return false;
    }
  }



  // -----------

  public static function check_sql(){
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"]);
    if(!$db){
      $res = array("status"=>"error","message"=>"SQLに接続できません。システム設定を確認してください。","code"=>"sql-001");
    }
    else{
      $res = array("status"=>"ok","database"=>$db);
    }
    return $res;
  }
  public static function check_database($dbname=""){

    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];

    $sql = new \mysqli($config["host"] , $config["user"] , $config["pass"]);

    $db = $sql->select_db($dbname);

    if(!$db){
      return array("status"=>"error","message"=>"SQLに接続できません。システム設定を確認してください。","code"=>"sql-001");
    }
    else{
      return array("status"=>"ok","database"=>$db);
    }

  }
  public static function create_database($dbname=""){
    if(!$dbname){
      return array("status"=>"error","message"=>"Database名が指定されていません。");
    }
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());

    $mysql = \mysqli_connect($config["host"] , $config["user"] , $config["pass"]);
    if($mysql->connect_error){
      return array("status"=>"error","message"=>"No Database-Access.","code"=>"mysq;-012");
    }

    $connect_db = $mysql->query('CREATE DATABASE '.$dbname);
    if(!$connect_db){
      return array("status"=>"error","message"=>"データベース作成失敗。 ","code"=>"mysql-013");
    }
    $mysql->close();

    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);
    if($db->connect_error){
      $res = array("status"=>"error","message"=>"Databaseに接続できません。","code"=>"sql-014");
    }
    else{
      $res = array("status"=>"ok","database"=>$db);
    }
    return $res;
  }
  public static function delete_database($dbname=""){
    if(!$dbname){
      return array("status"=>"error","message"=>"Database名が指定されていません。");
    }
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());

    $mysql = \mysqli_connect($config["host"] , $config["user"] , $config["pass"]);
    if($mysql->connect_error){
      return array("status"=>"error","message"=>"No Database-Access.","code"=>"mysq;-015");
    }

    $connect_db = $mysql->query('DROP DATABASE '.$dbname);
    $mysql->close();
    if(!$connect_db){
      $res = array("status"=>"error","message"=>"データベース削除失敗。 ","code"=>"mysql-016");
    }
    else{
      $res = array("status"=>"ok","database"=>$dbname);
    }
    return $res;
  }

  public static function check_table($dbname="",$table=""){
    if(!$table){
      return array("status"=>"error","message"=>"Table名の指定がありません。");
    }

    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);

    $sql = "SHOW TABLES LIKE '".$table."'";

    $res = $db->query($sql);

    if($res->num_rows > 0){
      return array("status"=>"ok");
    }
    else{
      return array("status"=>"error","message"=>"TABLEが存在しません。","code"=>"sql-021","sql"=>$sql);
    }
  }
  public static function create_table($dbname="",$table=""){
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);

    // 存在確認
    $flg = self::check_table($dbname,$table);
    if($flg["status"]==="ok"){
      return array("status"=>"error","message"=>"すでに作られています。","code"=>"sql-032");
    }
    
    $columnValue = self::makeColumn($table);
    if($columnValue["status"] === "error"){
      return $columnValue;
    }

    $sql = "CREATE TABLE ".$table." (".implode(",",$columnValue["data"]).") charset=utf8";

    $ret = $db->query($sql);
    if($ret){
      // indexセット
      $index_count = self::setIndex($db , $table);

      return array("status"=>"ok","index_count"=>$index_count);
    }
    else{
      return array("status"=>"error","message"=>"テーブル作成失敗 : ".$ret,"code"=>"sql-033");
    }
  }
  // カラム情報の作成
  public static function makeColumn($table=""){
    if(!$table){
      return array("status"=>"error","message"=>"Tableが指定されていません。","code"=>"sql-035");
    }
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    if(!isset($config["tables"][$table]["columns"])){
      return array("status"=>"error","message"=>"設定データがありません。","code"=>"sql-034");
    }
    $keys = array();
    foreach($config["tables"][$table]["columns"] as $name => $columns){
      $type = $columns["type"];
      $leng = ($columns["length"]) ? "(".$columns["length"].")" : "";
      $opt  = $columns["option"];
      $key = $name." ".$type.$leng." ".$opt;
      array_push($keys,$key);
    }

    return array("status"=>"ok","data"=>$keys);
  }

  public static function setIndex($db , $table=""){
    if(!$db){
      return array("status"=>"error","message"=>"DBが指定されていません。","code"=>"sql-035.1");
    }
    if(!$table){
      return array("status"=>"error","message"=>"Tableが指定されていません。","code"=>"sql-035.2");
    }
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    if(!isset($config["tables"][$table]["columns"])){
      return array("status"=>"error","message"=>"設定データがありません。","code"=>"sql-034");
    }
    $indexes = array();
    foreach($config["tables"][$table]["columns"] as $name => $columns){
      if(!isset($columns["index"]) || !$columns["index"]){continue;}
      if(!isset($indexes[$columns["index"]])){
        $indexes[$columns["index"]] = array();
      }
      array_push($indexes[$columns["index"]],$name);
    }

    // index処理
    if(array_keys($indexes) && count(array_keys($indexes)) > 0){
      $index_count = 0;
      foreach($indexes as $index_key => $index_columns){
        $columns = array();
        for($i=0; $i<count($index_columns); $i++){
          array_push($columns,$index_columns);
        }
        $sql = "ALTER TABLE ".$table." ADD INDEX ".$index_key."(".implode(",",$index_columns).")";
        $ret = $db->query($sql);
        if($ret){
          $index_count++;
        }
      }
      return array("status"=>"ok","data"=>$keys,"index_count"=>$index_count);
    }

    // no-index
    else{
      return array("status"=>"ok","data"=>$keys,"index_count"=>0);
    }
  }
  
  public static function check_data($dbname="" , $table="" , $whereValue=array()){
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);

    $ret = $db->query("select exists (select * from ".$table." where ".$whereValue.")");
    $db->close();
    if(mysqli_fetch_assoc($ret)){
      return array("status"=>"ok");
    }
    else{
      return array("status"=>"error","message"=>"データが存在しません。","code"=>"sql-031");
    }
  }

  public static function data_insert($dbname="",$table="",$dataValues=array()){
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);

    $keys = array();
    $vals = array();
    foreach($dataValues as $key => $val){
      array_push($keys , $key);
      $val = json_encode($val , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      array_push($vals , $val);
    }
    if(!$keys || !count($keys) || !$vals || !count($vals)){
      return array("status"=>"error" , "message"=>"登録するデータがありません。","code"=>"sql-051.1");
    }
    $sql = "INSERT INTO ".$table." (".implode(",",$keys).") VALUES (".implode(",",$vals).")";
    $res = $db->query($sql);

    // // auto_increment
    // $sql2 = "SELECT auto_increment FROM information_schema.tables WHERE table_name = '".$table."'";
    // $res2 = $db->query($sql);

    // $last_id = mysql_insert_id();
    $last_id = $db->insert_id;

    $db->close();
    if($res){
      return array("status"=>"ok","data"=>$dataValues,"auto_increment"=>$last_id);
    }
    else{
      $json = json_encode($dataValues,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return array("status"=>"error" , "message"=>"データ追加登録に失敗しました。(".$json.")"  , "code"=>"sql-051","sql"=>$sql);
    }
  }

  public static function data_update($dbname="",$table="",$dataValues=array(),$wheres=array()){
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);

    $vals = array();
    foreach($dataValues as $key => $val){
      $val = json_encode($val , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      array_push($vals , $key."=".$val);
    }

    $dataValue = implode(",",$vals);
    $whereArray = array();
    foreach($wheres as $key => $value){
      array_push($whereArray , $key."='".$value."'");
    }

    $whereValue = (count($whereArray)) ? "WHERE ".implode(",",$whereArray) : "";

    $sql = "UPDATE ".$table." SET ".$dataValue." ".$whereValue;
    $res = $db->query($sql);

    $db->close();

    if($res){
      return array("status"=>"ok");
    }
    else{
      return array("status"=>"error" , "message"=>"データ更新登録に失敗しました。","code"=>"sql-054","sql"=>$sql);
    }
  }
  public static function data_delete($dbname="",$table="",$wheres=array()){
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);

    if(!$wheres){
      return array("status"=>"error" , "message"=>"削除する指定keyがありません。","code"=>"sql-054.8");
    }

    $whereArray = array();
    foreach($wheres as $key => $value){
      array_push($whereArray , $key."='".$value."'");
    }
    $whereValue = (count($whereArray)) ? "WHERE ".implode(",",$whereArray) : "";

    $sql = "DELETE FROM ".$table." ".$whereValue;
    $res = $db->query($sql);

    $db->close();
    if($res){
      return array("status"=>"ok");
    }
    else{
      return array("status"=>"error" , "message"=>"データ削除に失敗しました。","code"=>"sql-054.9" , "sql"=>$sql);
    }
  }


  public static function data_select($dbname="" , $table , $keys=array() , $wheres=array()){
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
// die($dbname);
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);

    // table-check
    $check = self::check_table($dbname,$table);
    if($check["status"] === "error"){
      return $check;
    }

    // wheres
    $whereArray = array();
    foreach($wheres as $key => $value){
      array_push($whereArray , $key."='".$value."'");
    }
    $whereValue = (count($whereArray)) ? "WHERE ".implode(",",$whereArray) : "";

    $key = (gettype($keys)==="array") ? implode(",",$keys) : "";
    $key = ($key) ? $key : "*";

    $sql = "SELECT ".$key." FROM ".$table." ".$whereValue;
    $ret = $db->query($sql);

    $db->close();
    if(!$ret){
      return array("status"=>"error","message"=>"データ表示に失敗しました。".$sql ,"code"=>"sql-052");
    }
    else{
      $datas = array();
      while($row = mysqli_fetch_assoc($ret)){
        array_push($datas,$row);
      }
      return array("status"=>"ok","data"=>$datas);
    }
  }

  public static function view_table_columns($dbname="",$table=""){
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);

    $sql = "DESC ".$table;
    $ret = $db->query($sql);

    $db->close();
    if(!$ret){
      return array("status"=>"error","message"=>"Table情報の取得に失敗しました。(".$table.")" ,"code"=>"sql-053");
    }
    else{
      $datas = array();
      while($row = mysqli_fetch_assoc($ret)){
        array_push($datas,$row);
      }
      return array("status"=>"ok","data"=>$datas);
    }
  }


  
  public static function data_load($dbname="",$table="",$keys=array(),$wheres=array()){

    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);

    if(!$db){
      $check  = self::check_database();
      if($check["status"] === "error"){
        return $check;
      }
      $db = $check["database"];
    }

    $check = self::check_table($dbname,$table);
    $db->close();
    if($check["status"] === "error"){
      return $check;
    }

    return self::data_select($dbname,$table,$keys,$wheres);
  }
  public static function data_del($dbname="",$table="",$datas=array(),$wheres=array()){
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);

    if(!$db){
      $check  = self::check_database();
      if($check["status"] === "error"){
        return $check;
      }
      $db = $check["database"];
    }
    
    $check = self::check_table($dbname,$table);
    $db->close();

    if($check["status"] === "error"){
      return $check;
    }

    return self::data_delete($dbname,$table,$wheres);
  }

  public static function getTableLists($dbname=""){
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);
    if(!$db){
      $check  = self::check_database();
      if($check["status"] === "error"){
        return $check;
      }
      $db = $check["database"];
    }

    $sql = "SHOW TABLES";
    $ret = $db->query($sql);

    $db->close();
    if($ret){
      $datas = array();
      while($row = mysqli_fetch_assoc($ret)){
        array_push($datas , $row["Tables_in_".$dbname]);
      }
      return array("status"=>"ok","data"=>$datas);
    }
    else{
      return array("status"=>"error","message"=>"TABLEが存在しません。","data"=>array());
    }
  }

  public static function getTableIndexLists($dbname="",$tableName=""){
    if(!$tableName){
      return array("status"=>"error","message"=>"Tableの指定がありません。","data"=>array());
    }
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dbname = ($dbname) ? $dbname : $config["database"];
    unset($db);
    $db = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $dbname);
    if(!$db){
      $check  = self::check_database();
      if($check["status"] === "error"){
        return $check;
      }
      $db = $check["database"];
    }
    $sql = "SHOW INDEX FROM ".$tableName;
    $ret = $db->query($sql);

    $db->close();
    if($ret){
      $datas = array();
      while($row = mysqli_fetch_assoc($ret)){
        if($row["Key_name"] === "PRIMARY"){continue;}
        if(!isset($datas[$row["Key_name"]])){
          $datas[$row["Key_name"]] = array();
        }
        array_push($datas[$row["Key_name"]] , $row["Column_name"]);
      }
      return array("status"=>"ok","data"=>$datas);
    }
    else{
      return array("status"=>"error","message"=>"TABLEが存在しません。","data"=>array());
    }
  }

  // public static $indexKeys = array();
  // public static function getIndexKeys($table=""){
  //   if(!$table){return null;}
  //   if(isset(self::$indexKeys[$table])){return self::$indexKeys[$table];}

  //   $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
  //   if(!$config || !isset($config["dir"])){
  //     die("Error (code:json-002) Config-error.");
  //   }
  //   $keys = array();
  //   foreach($config["tables"][$table]["columns"] as $key => $val){
  //     if(!isset($val["index"]) || !$val["index"]){continue;}
  //     if(!isset($keys[$val["index"]])){
  //       $keys[$val["index"]] = array();
  //     }
  //     $keys[$val["index"]][] = $key;
  //   }
  //   self::$indexKeys[$table] = $keys;
  //   return self::$indexKeys[$table];
  // }

  // public static function getIndexPath($table="",$data=array()){
  //   if(!$table || !$data){return "";}
  //   $keys = self::getIndexKeys($table);
  //   $newDatas = array();
  //   if($keys){
  //     foreach($keys as $key => $arr){
  //       $val = array();
  //       for($i=0; $i<count($arr); $i++){
  //         $val[] = (isset($data[$arr[$i]])) ? $data[$arr[$i]] : "";
  //       }
  //       array_push($newDatas , implode("_",$val));
  //     }
  //   }
  //   return implode("/",$newDatas);
  // }


}