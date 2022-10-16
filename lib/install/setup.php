<?php
namespace lib\install;
/**
 * Path    : mynt/install/php/setup.php
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.1.0
 * Summary : フレームワークの初期設定用インストール処理モジュール
 * Example : 
 */
/**
 * Myntstudio install modules
 * 1. check-data
 * 2. choise data-type [ json , mysql ]
 * 3. input-data
 * 4. json  : copy data-file
 *    mysql : make-database , make-table , insert-data
 */

class setup{

  public static function save_all(){
    self::save_database_data();
    self::save_account_data();
    self::redirect_success();
  }

  public static function save_database(){
    self::save_database_data();
    self::redirect_success();
  }

  public static function save_database_new(){

    $path = \mynt::exec('lib\data\database','getSettingFile');
    // newの時にdata/database.jsonが存在する場合は削除する
    if(is_file($path)){
      unlink($path);
    }

    // database.jsonのメモリキャッシュ情報をクリアする
    $res = \mynt::exec('\lib\data\database','getSetting_clear');


    self::save_database_data("new");
    self::save_account_data();
    self::redirect_success();
  }

  public static function change_database(){
    self::save_database_data("change");
    self::redirect_success();
  }



  public static function save_account_data(){
    $login_mail = $_POST["auth"]["login_mail"];
    $login_pw   = $_POST["auth"]["login_pw"];
    $login_pw2  = $_POST["auth"]["login_pw2"];
    $message    = self::check_account($login_mail , $login_pw , $login_pw2);
    if($message){
      self::view_error($message);
    }

    // save-account
    $datas = array(
      "auth"  => $_POST["auth"]["auth"],
      "mail"  => $_POST["auth"]["login_mail"],
      "pass"  => \mynt::exec('\lib\auth\password','encode',array($_POST["auth"]["login_pw"]))
    );
    $res = \mynt::exec('\lib\auth\data' , 'save' , array($datas));

    // $res = \mynt::data_save('' , 'lib_account' , array($datas));
    if($res["status"] === "error"){
      die("Don't make data/tables/lib_account.json.");
    }

  }

  public static function save_database_data($flg="edit"){

    // 新規処理 or 既存編集
    // $path = \mynt::exec('lib\data\database','getSettingFile');
    // $flg  = $mode ? $mode : (is_dir($path) ? "new" : "edit");

    // 入力値チェック(database)
    $message = self::check_database($_POST["database"]["type"] , $_POST["database"]["database"]);
    if($message){
      self::view_error($message);
    }

    // flg = edit の場合は設定内容を読み込む
    if($flg === "edit"){
      $setting = \mynt::exec('\lib\data\database','getSetting');
      if(!$setting){
        $flg = "new";
      }
      // databaseが切り替わった場合はjson-dataフォルダ名を同時に切り替える
      else if($_POST["database"]["database"] !== $setting["database"]){
        $dir = \mynt::exec('\lib\data\database','getDir');
        $before_path = $dir . $setting["database"];
        $after_path  = $dir . $_POST["database"]["database"];
        rename($before_path , $after_path);
      }
    }
    
    // save-database
    $res = \mynt::exec('\lib\data\database' , "save" , array($_POST["database"]));
    if($res === false){
      $db_setting_file = \mynt::exec('\lib\data\database','getSettingFile');
      self::view_error($db_setting_file ."ファイルが作成できません。");
    }

    // default-data-copy
    $dir = \mynt::exec('\lib\data\database','getDir');
    $database_dir = $dir . $_POST["database"]["database"]."/";
    if(!is_dir($database_dir)){
      mkdir($database_dir , 0777 , true);
    }

    // lib-auth-data
    if($flg === "new"){
      $default_files = scandir("lib/data/default/");
      for($i=0; $i<count($default_files); $i++){
        if(!preg_match("/\.json$/",$default_files[$i])){continue;}
        copy("lib/data/default/".$default_files[$i] , $database_dir.$default_files[$i]);
      }
    }
  }

  public static function view_error($message=""){
    $_POST["message_error"] = $message;
    \mynt::page($_POST["err_f"] , $_POST["err_p"]);
    exit();
  }
  public static function redirect_success(){
    if(isset($_POST["redirect"]) && $_POST["redirect"]){
      \mynt::exec('\lib\common\url',"setUrl",array($_POST["redirect"]));
    }
    else{
      $uri = \mynt::exec('\lib\common\url' , "getUri");
      \mynt::exec('\lib\common\url' , "setUrl" , array($uri));
    }
  }





  public static function check_account($mail,$pass,$pass2){
    $mail_exp = "|^[0-9a-z_./?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$|";

    if(!$mail || !$pass || !$pass2){
      return "未入力の項目があります";
    }
    if(!preg_match($mail_exp , $mail)){
      return "メールアドレスの形式が不正です";
    }
    if($pass !== $pass2){
      return "パスワードが違います";
    }
  }

  public static function check_database($type,$name){
    if(!$type || !$name){
      return "未入力の項目があります";
    }
  }







  public static function branch_point($type=""){

    // install-post
    if(isset($_REQUEST["mode"]) && $_REQUEST["mode"] === "install_post"){
      self::post($type);

      // after-setup-page (admin-panel)
      \mynt::exec("\\lib\\common\\url" , "setUrl" , array("system.php"));
    }

    // install-input-view
    else{
      // self::view();
      \mynt::exec('\lib\install\setup',"");
    }
    exit();
  }

  

  // public static function viewInputs(){die("aaa");
  //   \mynt::exec("\\mynt" , "checkModuleLoad" , "\\mynt\\plugin\\data\\json");
  //   $config = \mynt::exec("\\lib\\data\\data_json" , "loadInstallConfig" , "mynt/install/config/");
    
  //   $html="";
  //   foreach($config as $type => $datas){

  //     $html .= "<h2>".$type."</h2>";
  //     foreach($datas as $name => $data){
  //       $req = (isset($data["required"]))?$data["required"]:0;
  //       $html .= "<div class='post-data'>";
  //       $html .= "<label>".$name."</label>";
  //       $html .= "<p>".$data["disc"]."</p>";
  //       $html .= "<input type='hidden' name='type[".$type."][".$name."]' value='".$data["type"]."'>";
  //       $html .= "<input type='hidden' name='length[".$type."][".$name."]' value='".$data["length"]."'>";
  //       $html .= "<input type='text' name='value[".$type."][".$name."]' value='".$data["value"]."' ".(($req)?"required":"").">";
  //       if($req){
  //         $html .= "<span class='annotation'>*必須</span>";
  //       }
  //       $html .= "</div>";
  //     }
  //   }
  //   return $html;
  // }

  public static function post($type){

    // account-check
    if(!isset($_REQUEST["account"]["login_id"])
    || !isset($_REQUEST["account"]["login_mail"])
    || !isset($_REQUEST["account"]["login_pw"])
    || !$_REQUEST["account"]["login_id"]
    || !$_REQUEST["account"]["login_mail"]
    || !$_REQUEST["account"]["login_pw"]
    ){
      die("Error (code:init:003) Query data shortage.");
      return false;
    }

    $config = \mynt::exec("\\lib\\common\\config" , "getData" , array());
    switch($type){
      case "mysql":
        $res = self::make_mysql($config , $_REQUEST["account"] , $_REQUEST["value"]);
        break;

      case "net":
        // $res = \mynt::exec("\\mynt\\lib\\data_net"   , "makeInit" , array($config , $_REQUEST["account"] , $_REQUEST["value"]));
        break;

      default :
        $res = self::make_json($config , $_REQUEST["account"] , $_REQUEST["value"]);
        // $res = \mynt::exec("\\mynt\\lib\\data_json"  , "makeInit" , array($config , $_REQUEST["account"] , $_REQUEST["value"]));
        break;
    }

    if($res["status"] === "error"){
      die("Error ".$res["message"]);
    }
  }

  // 
  public static function make_mysql($config="" , $accounts=array() , $values=array()){
    if(!$config || !$accounts || !$values){
      return array("status"=>"error","message"=>"No Config.","code"=>"setup-001");
    }

    // Database -----
    $mysql = new \mysqli($config["host"] , $config["user"] , $config["pass"]);
    if($mysql->connect_error){
      return array("status"=>"error","message"=>"No Database-Access.","code"=>"setup-002");
    }

    // database存在確認
    $db = null;
    $check = \mynt::exec("\\mynt\\lib\\data_mysql","check_database",array());
    if($check["status"] === "error"){
      // database作成
      $connect_db = $mysql->query('CREATE DATABASE '.$config["database"]);
      if(!$connect_db){
        return array("status"=>"error","message"=>"データベース作成失敗。 ".$connect_db,"code"=>"setup-003");
      }
      $mysql->close();
      $db = new \mysqli($config["host"] , $config["user"] , $config["pass"] , $config["database"]);
      if($db->connect_error){
        return array("status"=>"error","message"=>"No Database-Access.","code"=>"setup-004");
      }
    }
    else{
      $mysql->close();
      $db = new \mysqli($config["host"] , $config["user"] , $config["pass"] , $config["database"]);
    }

    // 
    $date = date("YmdHis");
    
    // Account-data -----
    $table = "account";
    // table作成
    if($db->query("SHOW TABLES LIKE '".$table."'")->num_rows === 0){
      $res = \mynt::exec("\\mynt\\lib\\data_mysql" , "create_table" , array("" , $table));
      if($res["status"] === "error"){
        print_r($res);exit();
      }
    }
    
    // データ登録
    if($db->query("SELECT * FROM ".$table)->num_rows === 0){
      $dataArray = array(
        "id"    => "admin",
        "auth"  => "admin",
        "mail"  => $accounts["login_mail"],
        "md5"   => \md5($accounts["login_pw"]),
        "date"  => $date,
        "entry" => $date
      );
      $res = \mynt::exec("\\mynt\\lib\\data_mysql" , "data_insert" , array("",$table,$dataArray));
      if($res["status"] === "error"){
        print_r($res);exit();
      }
    }

    // Config-data -----
    // table登録
    $table = "config";
    if($db->query("SHOW TABLES LIKE '".$table."'")->num_rows !== 1){
      $res = \mynt::exec("\\mynt\\lib\\data_mysql" , "create_table" , array("" , $table));
      if($res["status"] === "error"){
        print_r($res);exit();
      }
    }
    
    // データ登録
    if($db->query("SELECT * FROM ".$table)->num_rows === 0){
      foreach($values as $type => $data){
        foreach($data as $name => $value){
          $dataArray = array(
            "type"  => $type,
            "name"  => $name,
            "value" => $value,
            "date"  => $date,
            "entry" => $date
          );
          $res = \mynt::exec("\\mynt\\lib\\data_mysql" , "data_insert" , array("",$table,$dataArray));
          if($res["status"] === "error"){
            print_r($res);exit();
          }
        }
      }
    }

    return true;
  }

  public static function make_json($config="" , $accounts=array() , $values=array()){
    if(!$config || !$accounts || !$values){
      die("Error (code:init-002) No Data.");
    }

    $date = date("YmdHis");

    if(!is_dir($config["dir"])){
      mkdir($config["dir"] , 0777 , true);
    }

    // account
    $dir = $config["dir"] . $config["database"]."/";
    if(!is_dir($dir)){
      mkdir($dir , 0777 , true);
    }
// die($dir);
    $data_account = array(
      "auth"   => $accounts["auth"],
      "mail"   => $accounts["login_mail"],
      "md5"    => \md5($accounts["login_pw"]),
      "date"   => $date,
      "entry"  => $date
    );
    $path = $dir."account.json";
    $json_account = json_encode($data_account , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    file_put_contents($path , $json_account."\n" , FILE_APPEND);

    // config
    $path = $dir."config.json";
    // $path = $dir."config.json";
    $num = 1;
    foreach($values as $key => $v){
      foreach($v as $name => $value){
        $data = array(
          "id"    => $num,
          "type"  => $key,
          "name"  => $name,
          "value" => $value,
          "date"  => $date,
          "entry" => $date
        );
        $json_value = json_encode($data , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        file_put_contents($path , $json_value."\n" , FILE_APPEND);
        $num++;
      }
    }

    return true;
  }

  
}