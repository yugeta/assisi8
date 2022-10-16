<?php
/**
 * Unit Test
 * Path    : system.php
 * Author  : Yugeta.Koji (MYNT Inc.)
 * Date    : 2019.07.12
 * Ver     : 0.4.0
 * Summary : php-module-test
 * Howto   : $ php unitTest.php
 */

 // init
require_once "mynt/lib/php/_mynt.php";
require_once "mynt/lib/php/data.php";

if(!isset($_SERVER['SCRIPT_URI']) && isset($argv)){
  for($i=0,$c=count($argv);$i<$c;$i++){
    if(!$argv[$i]){continue;}
    //各クエリの分解
    $q = explode("=",$argv[$i]);
    if(count($q)<2){continue;}
    if($q[0]!=''){
      //requestに格納
      $key = $q[0];
      $val = implode("=",array_slice($q,1));
      $_REQUEST[$key]=$val;
    }
  }
}

unitTest::init();
// $unitTest = new unitTest();
// $unitTest->init();

//
class unitTest{

  // manual
  function manual(){
    echo "COMMAND : " . $_SERVER['PHP_SELF'] . " mode=mysql db=%Database名% table=%登録済みTable名%".PHP_EOL;
    echo "COMMAND : " . $_SERVER['PHP_SELF'] . " mode=mysql-set db=%Database名% table=%登録済みTable名%".PHP_EOL;

  }

  // Request-data (CLI対応)
  function init(){
    if(!isset($_REQUEST["mode"]) || !$_REQUEST["mode"]){return self::manual();}
    // if($_REQUEST["mode"] === "mysql"){
    //   self::test_mysql();
    // }
    switch($_REQUEST["mode"]){
      case "mysql"     : self::test_mysql(); break;
      case "mysql-set" : self::test_mysql_set(); break;
      case "data-json-save" : self::test_data_json_save(); break;
      case "hash_1" : self::test_hash_1(); break;
      case "hash_2" : self::test_hash_2(); break;
      case "multi_sort" : self::test_multi_sort(); break;
      case "sendMail" : self::sendMail($_REQUEST["to"],$_REQUEST["from"],$_REQUEST["subject"],$_REQUEST["message"]); break;
      case "script_name" : self::test_script_name(); break;
      case "ini-check" : self::ini_check(); break;
      case "test-save" : self::test_save(); break;
      case "test-mkdir" : self::test_mkdir(); break;
      default:break;
    }
    
  }

  // test-001 : mysql
  function test_mysql(){
    // query-check
    $flg = 0;
    if(!isset($_REQUEST["db"]) || !$_REQUEST["db"]){$flg++;}
    if(!isset($_REQUEST["table"]) || !$_REQUEST["table"]){$flg++;}
    if($flg>0){
      self::manual();
      exit();
    }


    // Database操作 ==========

    echo "[Database-mysql]".PHP_EOL;


    // MYSQLアクセス
    echo "- check mynt/config/table.json : ";
    $res = \mynt::execution("\\mynt\\lib\\data" , "column" , array());
    echo $res["status"].(isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;

    // Database作成
    echo "- create-database : ";
    $res = \mynt::execution("\\mynt\\lib\\data_mysql" , "create_database" , array($_REQUEST["db"]));
    echo $res["status"].(isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;

    // 作成したデータベースを格納
    $db = $res["database"];

    // Table作成
    echo "- create-table : ";
    $res = \mynt::execution("\\mynt\\lib\\data_mysql" , "create_table" , array($db,$_REQUEST["table"]));
    echo $res["status"].(isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;

    // 作成されたTableの情報取得(DESC)
    echo "- view-table-columns : ";
    $res = \mynt::execution("\\mynt\\lib\\data_mysql" , "view_table_columns" , array($db,$_REQUEST["table"]));
    if($res["status"] === "error"){
      echo $res["status"].(isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    }
    else{
      print_r($res["data"]);
    }
    echo PHP_EOL;


    // データ追加登録
    echo "- add-data : ";
    $dataValues = array(
      "auth"  => "admin",
      "mail"  => "test@test.com",
      "md5"   => md5("admin"),
      "date"  => date("YmdHis"),
      "entry" => date("YmdHis"),
    );
    $res = \mynt::execution("\\mynt\\lib\\data_mysql" , "data_insert" , array($db,$_REQUEST["table"],$dataValues));
    echo $res["status"] . (isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;

    // 登録されたデータの確認(SELECT)
    echo "- view-data-1 : ";
    $keys = array();
    $wheres = array("mail"=>"test@test.com");
    $res = \mynt::execution("\\mynt\\lib\\data_mysql" , "data_select" , array($db,$_REQUEST["table"],$keys,$wheres));
    if($res["status"] === "ok"){
      print_r($res["data"]);
    }
    else{
      echo "Error-".$res["message"].PHP_EOL;
    }
    echo PHP_EOL;
    $id = $res["data"][0]["id"];


    // データ修正登録
    echo "- update-data : ";
    $dataValues = array(
      "auth"  => "support",
      "date"  => date("YmdHis"),
      "entry" => date("YmdHis"),
    );
    $wheres = array(
      "mail"=>"test@test.com"
    );
    $res = \mynt::execution("\\mynt\\lib\\data_mysql" , "data_update" , array($db,$_REQUEST["table"],$dataValues,$wheres));
    echo $res["status"] . (isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;

    // 登録されたデータの確認(SELECT)
    echo "- view-data-2 : ";
    $keys = array();
    $wheres = array();
    $res = \mynt::execution("\\mynt\\lib\\data_mysql" , "data_select" , array($db,$_REQUEST["table"],$keys,$wheres));
    if($res["status"] === "ok"){
      print_r($res["data"]);
    }
    else{
      echo "Error-".$res["message"].PHP_EOL;
    }
    echo PHP_EOL;




    // Databaseを閉じる
    $db->close();

    // Database削除
    echo "- delete-database : ";
    $res = \mynt::execution("\\mynt\\lib\\data_mysql" , "delete_database" , array($_REQUEST["db"]));
    echo $res["status"].(isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;
  }




  // teat-002
  function test_mysql_set(){
    // query-check
    $flg = 0;
    if(!isset($_REQUEST["db"]) || !$_REQUEST["db"]){$flg++;}
    // if(!isset($_REQUEST["table"]) || !$_REQUEST["table"]){$flg++;}
    if($flg>0){
      self::manual();
      exit();
    }

    // Database操作 ==========

    echo "[Database-mysql-set]".PHP_EOL;


    // MYSQLアクセス
    echo "- check mynt/config/table.json : ";
    $res = \mynt::execution("\\mynt\\lib\\data" , "column" , array());
    echo $res["status"].(isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;

    // Database作成
    echo "- create-database : ";
    $res = \mynt::execution("\\mynt\\lib\\data_mysql" , "create_database" , array($_REQUEST["db"]));
    echo $res["status"].(isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;

    // 作成したデータベースを格納
    $db = $res["database"];

    // Table作成
    echo "- create-table : ";
    $res = \mynt::execution("\\mynt\\lib\\data_mysql" , "create_table" , array($db,$_REQUEST["table"]));
    echo $res["status"].(isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;

    // 作成されたTableの情報取得(DESC)
    echo "- view-table-columns : ";
    $res = \mynt::execution("\\mynt\\lib\\data_mysql" , "view_table_columns" , array($db,$_REQUEST["table"]));
    if($res["status"] === "error"){
      echo $res["status"].(isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    }
    else{
      print_r($res["data"]);
    }
    echo PHP_EOL;


    // データ追加登録
    echo "- save : ";
    $dataValues = array(
      "auth"  => "admin",
      "mail"  => "test@test.com",
      "md5"   => md5("admin"),
      "date"  => date("YmdHis"),
      "entry" => date("YmdHis"),
    );
    $res = \mynt::execution("\\mynt\\lib\\data" , "save" , array($db , $_REQUEST["table"] , $dataValues , array()));
    echo $res["status"] . (isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;

    // 登録されたデータの読込(SELECT)
    echo "- load-1 : ";
    $keys = array("id","auth","mail","md5");
    $wheres = array("mail"=>"test@test.com");
    $res = \mynt::execution("\\mynt\\lib\\data" , "load" , array($db , $_REQUEST["table"] , $keys , array()));
    if($res["status"] === "ok"){
      print_r($res["data"]);
    }
    else{
      echo "Error-".$res["message"].PHP_EOL;
    }
    echo PHP_EOL;
    $id = $res["data"][0]["id"];

    // データ修正登録
    echo "- save : ";
    $dataValues = array(
      "auth"  => "test",
      "mail"  => "test@test.com",
      "md5"   => md5("admin"),
      "date"  => date("YmdHis"),
      "entry" => date("YmdHis"),
    );
    $wheres = array(
      "id" => $id
    );
    $res = \mynt::execution("\\mynt\\lib\\data" , "save" , array($db , $_REQUEST["table"] , $dataValues ,$wheres));
    echo $res["status"] . (isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;

    // 登録されたデータの読込(SELECT)
    echo "- load-2 : ";
    $keys = array("id","auth","mail","md5");
    $wheres = array("mail"=>"test@test.com");
    $res = \mynt::execution("\\mynt\\lib\\data" , "load" , array($db , $_REQUEST["table"] , $keys , array()));
    if($res["status"] === "ok"){
      print_r($res["data"]);
    }
    else{
      echo "Error-".$res["message"].PHP_EOL;
    }
    echo PHP_EOL;
    $id = $res["data"][0]["id"];


    // データ削除
    echo "- rm : ";
    $wheres = array(
      "id"=>$id
    );
    $res = \mynt::execution("\\mynt\\lib\\data" , "remv" , array($db , $_REQUEST["table"] , $wheres));
    echo $res["status"] . (isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;

    // 登録されたデータの確認(SELECT)
    echo "- load-3 : ";
    $keys = array("id","auth","mail","md5");
    $wheres = array("mail"=>"test@test.com");
    $res = \mynt::execution("\\mynt\\lib\\data" , "load" , array($db , $_REQUEST["table"] , $keys , array()));
    if($res["status"] === "ok"){
      print_r($res["data"]);
    }
    else{
      echo "Error-".$res["message"].PHP_EOL;
    }
    echo PHP_EOL;




    // Databaseを閉じる
    $db->close();

    // Database削除
    echo "- delete-database : ";
    $res = \mynt::execution("\\mynt\\lib\\data_mysql" , "delete_database" , array($_REQUEST["db"]));
    echo $res["status"].(isset($res["message"])?" ".$res["message"]:"").PHP_EOL;
    echo PHP_EOL;
  }

  public static function test_data_json_save(){

  }

  public static function test_hash_1(){
    $data = array();
    $data["b"] = array(1,2,3);
    $data["a"] = array(4,5,6);
    $data["b"][] = "add";
    print_r(array_keys($data));
  }

  public static function test_hash_2(){
    $data1 = array(
      "id"  => "1",
      "val" => "aaa"
    );
    $data2 = array(
      "id"  => "2",
      "test" => "bbb"
    );
    print_r(array_merge($data2,$data1));
  }

  public static function test_multi_sort(){
    $array_main = array(
      array(
        "id" => 1,
        "aa" => "aa1",
        "bb" => "aa2"
      ),
      array(
        "id" => 2,
        "aa" => "aa2",
        "bb" => "aa1"
      ),
      array(
        "id" => 3,
        "aa" => "aa0",
        "bb" => "aa3"
      ),
      array(
        "id" => 4,
        "aa" => "aa1",
        "bb" => "aa3"
      )
    );

    $arr1 = array();
    $arr2 = array();
    $arr3 = array();
    for($i=0; $i<count($array_main); $i++){
      $arr1[] = $array_main[$i]["id"];
      $arr2[] = $array_main[$i]["aa"];
      $arr3[] = $array_main[$i]["bb"];
    }
    // sort($arr1);
    // sort($arr2 , SORT_ASC);
    // sort($arr3);

    // print_r($array_main);
    // array_multisort($arr2 , SORT_ASC , $arr3 , SORT_DESC , $array_main);
    // $test = SORT_ASC;
    // array_multisort($arr2 ,SORT_ASC , $array_main);
    // // array_multisort($array_main , SORT_ASC , $arr2);
    // // call_user_func_array('array_multisort', array($arr2 , SORT_ASC ,$array_main));
    // // call_user_func_array('array_multisort', array($arr2,SORT_ASC,$array_main));
    // print_r($array_main);
    // print_r($arr2);

    $testArr = self::
    array_msort($array_main , array("aa"=>"SORT_DESC"));
    print_r($testArr);

    
  }

  public static function array_msort($array, $cols)
  {
      $colarr = array();
      foreach ($cols as $col => $order) {
          $colarr[$col] = array();
          foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
      }
      $eval = 'array_multisort(';
      foreach ($cols as $col => $order) {
          $eval .= '$colarr[\''.$col.'\'],'.$order.',';
      }
      $eval = substr($eval,0,-1).');';
      eval($eval);
      $ret = array();
      foreach ($colarr as $col => $arr) {
          foreach ($arr as $k => $v) {
              $k = substr($k,1);
              if (!isset($ret[$k])) $ret[$k] = $array[$k];
              $ret[$k][$col] = $array[$k][$col];
          }
      }
      return $ret;

  }

  public static function sendMail($to , $from , $subject , $message){
    if(!$to || !$from || !$subject || !$message){
      echo "Error ! shortage-query.".PHP_EOL;
      echo "Ex) php unitTest.php mode=sendMail to=youemail@example.com from=system@example.com subject=test message=comment...".PHP_EOL;
      exit();
    }
    $res = \mynt::exec("\\mynt\\lib\\mail","sendMail",array($to,$from,$subject,$message));

    echo "to : "   . $to.PHP_EOL;
    echo "from : " . $from.PHP_EOL;
    echo "subject : " . $subject.PHP_EOL;
    echo "message : " . $message.PHP_EOL;
    if($res){
      echo "Success...".PHP_EOL;
    }
    else{
      echo "Error ! don't send-mail...".PHP_EOL;
    }
  }


  public static function test_script_name(){
    // echo json_encode($_SERVER , JSON_PRETTY_PRINT);
    
    if(isset($argv)){
      $script = $argv[0];
    }
    else if(isset($_SERVER["argv"])){
      $script = $_SERVER["argv"][0];
    }
    else{
      $script = "";
    }
    echo $script.PHP_EOL;
    // echo $_SERVER['REQUEST_URI'].PHP_EOL;
    // echo \mynt::getScriptName().PHP_EOL;
  }

  public static function ini_check(){

    echo "<h1>ini-check</h1>";

    $keys = array(
      "default_charset",
      "set_time_limit",
      "session.gc_maxlifetime",
      "session.gc_probability",
      "session.gc_divisor",
      "session.use_only_cookies"
    );

    foreach($keys as $key){
      echo $key." : " . ini_get($key) .PHP_EOL;
    }
  }

  public static function test_save(){
    file_put_contents("data/test.txt" , date("YmdHis").",-test-" , FILE_APPEND);
    echo "make-file : data/test.txt".PHP_EOL;
  }

  public static function test_mkdir(){
    mkdir("test",0777,true);
    echo "mkdir : test/".PHP_EOL;
  }


}






