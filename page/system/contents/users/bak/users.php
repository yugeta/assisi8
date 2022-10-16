<?php
namespace mynt\system;

class users{


  // view - lists

  public static function getLists_tr(){
    $lists = self::getLists();
    $html ="";
    for($i=0; $i<count($lists); $i++){
      $id    = (isset($lists[$i]["id"]))    ? $lists[$i]["id"]    : "";
      $mail  = (isset($lists[$i]["mail"]))  ? $lists[$i]["mail"]  : "";
      $auth  = (isset($lists[$i]["auth"]))  ? $lists[$i]["auth"]  : "";
      $auth  = (isset($lists[$i]["auth"]))  ? $lists[$i]["auth"]  : "";
      $name  = (isset($lists[$i]["name"]))  ? $lists[$i]["name"]  : "";
      $memo  = (isset($lists[$i]["memo"]))  ? $lists[$i]["memo"]  : "";
      $entry = (isset($lists[$i]["entry"])) ? $lists[$i]["entry"] : "";

      $html .= "<tr data-id='".$id."'>";
      $html .= "<th class='num'>".($i+1)."</th>";
      $html .= "<td class='id'>".$id."</td>";
      $html .= "<td class='mail'>".$mail."</td>";
      $html .= "<td class='auth'>".$auth."</td>";
      $html .= "<td class='name'>".$name."</td>";
      $html .= "<td class='comment'>".str_replace("\n","<br>",$memo)."</td>";
      $html .= "<td class='entry'>".$entry."</td>";
      $html .= "</tr>";
    }
    return $html;
  }

  public static function getLists(){
    // $config   = \mynt\lib\data::getConfig();
    // $account  = array();
    // $property = array();

    $account = \mynt\lib\data::data_load(
      "",
      "account",
      array(),
      array()
    );
// die("b");
// print_r($account);exit();
    $property = \mynt\lib\data::data_load(
      "",
      "property",
      array(),
      array()
    );

    $res = array();
    if($account){
      for($i=0; $i<count($account); $i++){
        $id = $account[$i]["id"];
        $res[$id] = $account[$i];
      }
    }
    if($property){
      for($i=0; $i<count($property); $i++){
        $id = $property[$i]["id"];
        // if(!$property[$i]["id"] || !isset($res[$property[$i]["id"]]) || !$res[$property[$i]["id"]]){continue;}
        $res[$id]["name"] = ($property[$i]["name"]) ? $property[$i]["name"] : "";
        $res[$id]["memo"] = ($property[$i]["memo"]) ? $property[$i]["memo"] : "";
      }
    }
    
    $datas    = array();
    foreach($res as $value){
      array_push($datas , $value);
    }

    return $datas;
  }


  // add =====

  // id
  public static function makeAccountId(){
    return self::getMsTime().".".rand(0,999);
  }
  // micro-second
  public static function getMsTime(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }

  public static function request_add(){
    $update     = date("YmdHis");

    // validation
    $_REQUEST["errorMessage"] = "";

    if(!$_POST["account"]["password"]){
      $_REQUEST["errorMessage"] .= "<li>「パスワード」が未入力です。</li>";
    }
    else if($_POST["account"]["password"] !== $_POST["account"]["password2"]){
      $_REQUEST["errorMessage"] .= "<li>「パスワード確認」が違います。</li>";
    }

    $mail_exp = "|^[0-9a-z_./?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$|";
    if(!$_POST["account"]["mail"]){
      $_REQUEST["errorMessage"] .= "<li>「メールアドレス」が未入力です。</li>";
		}
		else if(!preg_match($mail_exp , $_POST["account"]["mail"])){
      $_REQUEST["errorMessage"] .= "<li>「メールアドレス」が間違っています。</li>";
		}
    // else if(self::getMail2Data($_POST["account"]["mail"])){
    //   $_REQUEST["errorMessage"] .= "<li>登録済みのメールアドレスです。</li>";
    // }

    if($_REQUEST["errorMessage"] !== ""){
      $_REQUEST["errorMessage"] = "<ol>".$_REQUEST["errorMessage"]."</ol>";
      return false;
    }

    // // 登録済みのiIDを確認
    // if(self::checkID($account_id)){
    //   $_REQUEST["errorMessage"]  = "<ol>";
    //   $_REQUEST["errorMessage"] .= "<li>ログインIDが既に登録されています。</li>";
    //   $_REQUEST["errorMessage"] .= "</ol>";
    //   return;
    // }

    $id = ($_POST["account"]["id"]) ? $_POST["account"]["id"] : self::makeAccountId();

    $dataAccount = array(
      "id"     => $id,
      "mail"   => $_POST["account"]["mail"],
      "auth"   => $_POST["account"]["auth"],
      "md5"    => md5($_POST["account"]["password"]),
      "date"   => $update,
      "entry"  => $update
    );
    $dataProperty = array(
      "id"     => $id,
      "name"   => $_POST["property"]["name"],
      "memo"   => $_POST["property"]["memo"],
      "date"   => $update,
      "entry"  => $update
    );

// die("b");
    // $config = \mynt\lib\data::getConfig();
    $res = \mynt::exec("\\mynt\\lib\\data" , "save" , array("","account",$dataAccount,array()));
    if($res["status"] === "error"){
      die("Error データが正常に追加できません。");
    }
    $res = \mynt::exec("\\mynt\\lib\\data" , "save" , array("","property",$dataProperty,array()));
    if($res["status"] === "error"){
      die("Error データが正常に追加できません。");
    }

    // switch($config["type"]){
    //   case "mysql":
    //     self::add_account_mysql($config  , $dataAccount);
    //     self::add_property_mysql($config , $dataProperty);
    //     break;

    //   case "net":
    //     self::add_account_net($config  , $dataAccount);
    //     self::add_property_net($config , $dataProperty);
    //     break;

    //   default:
    //     self::add_account_json($config  , $dataAccount);
    //     self::add_property_json($config , $dataProperty);
    //     break;
    // }
    if(isset($_REQUEST["redirect"]) && $_REQUEST["redirect"]){
      \mynt::exec("\\mynt\\lib\\url","setUrl",array($_REQUEST["redirect"]));
    }
    else{
      $url = \mynt::exec("\\mynt\\lib\\url","getUrl",array());
      \mynt::exec("\\mynt\\lib\\url","setUrl",array($url));
    }
    exit("Redirected...");
  }

  public static function checkRequest(){

  }

  // edit =====

  public static function request_edit(){

    // check
    if($_POST["account"]["password"] || $_POST["account"]["password2"]){
      if($_POST["account"]["password"] !== $_POST["account"]["password2"]){
        die("Error (code:mysql-010) パスワードが違います。");
      }
    }

    $update = date("YmdHis");


    $where = array(
      "id" => $_POST["account"]["id"]
    );

    // property
    $accountRes  = \mynt::execution("\\mynt\\lib\\data" , "load" , array("" , "account"  , array()  , $where));
    $propertyRes = \mynt::execution("\\mynt\\lib\\data" , "load" , array("" , "property"  , array()  , $where));
    if($accountRes["status"] === "error"){
      \mynt::execution("\\mynt\\lib\\error" , "view" , array("登録されていないアカウントです。"));
    }

    // // auth
    // $auth = null;
    // if(isset($_POST["account"]["auth"])){
    //   $auth = $_POST["account"]["auth"];
    // }
    // else{
    //   $auth = $accountRes["data"]["auth"];
    // }
    
    // account
    $dataAccount =array(
      "mail" => $_POST["account"]["mail"],
      // "auth" => $auth,
      "date" => $update
    );
    if($_POST["account"]["password"]){
      $account_datas["md5"] = md5($_POST["account"]["password"]);
    }

    if($propertyRes["status"] === "error"){
      $dataProperty =array(
        "id"    => $_POST["account"]["id"],
        "name"  => $_POST["property"]["name"],
        "memo"  => $_POST["property"]["memo"],
        "date"  => $update,
        "entry" => $update
      );
    }
    else{
      $dataProperty =array(
        "name" => $_POST["property"]["name"],
        "memo" => $_POST["property"]["memo"],
        "date" => $update
      );
    }
    

    // save
    $res = \mynt::execution("\\mynt\\lib\\data" , "save" , array("" , "account"  , $dataAccount  , $where));
    if($res["status"] === "error"){
      die("Error データが正常に修正できません。");
    }
    $res = \mynt::execution("\\mynt\\lib\\data" , "save" , array("" , "property" , $dataProperty , $where));
    if($res["status"] === "error"){
      die("Error データが正常に修正できません。");
    }

    // redirect
    if(isset($_REQUEST["redirect"]) && $_REQUEST["redirect"]){
      \mynt::exec("\\mynt\\lib\\url","setUrl",array($_REQUEST["redirect"]));
    }
    else{
      $url = \mynt::exec("\\mynt\\lib\\url","getUrl",array());
      \mynt::exec("\\mynt\\lib\\url","setUrl",array($url));
    }
    exit("redirected...");
  }

  public static function request_del(){
    $date = date("YmdHis");

    // account
    $dataAccount = array(
      "date" => $date
    );
    // \mynt\lib\data::data_delete(
    //   "account",
    //   array(
    //     "id"=>$_POST["id"]
    //   )
    // );

    // property
    $dataProperty = array(
      "date" => $date
    );
    // \mynt\lib\data::data_delete(
    //   "property",
    //   array(
    //     "id"=>$_POST["id"]
    //   )
    // );

    $wheres = array(
      "id" => $_REQUEST["id"]
    );

    $res = \mynt::execution("\\mynt\\lib\\data" , "del" , array("" , "account"  , $dataAccount  , $wheres));
    if($res["status"] === "error"){
      die("Error データが正常に削除できません。");
    }
    $res = \mynt::execution("\\mynt\\lib\\data" , "del" , array("" , "property" , $dataProperty , $wheres));
    if($res["status"] === "error"){
      die("Error データが正常に削除できません。");
    }

    // redirect
    if(isset($_REQUEST["redirect"]) && $_REQUEST["redirect"]){
      \mynt::exec("\\mynt\\lib\\url","setUrl",array($_REQUEST["redirect"]));
    }
    else{
      $url = "?p=users";
      \mynt::exec("\\mynt\\lib\\url","setUrl",array($url));
    }
    exit("redirected...");

  }

  public static function add_account_mysql($config,$dataAccount){
    $db = new \mysqli($config["host"] , $config["user"] , $config["pass"] , $config["database"]);
    if($db->connect_error){
      die("Error (code:mysql-002) No Database-Access.");
    }

    // データ追加
    $sql  = "INSERT INTO account (id,mail,auth,md5,date,entry) VALUES ('".$dataAccount["id"]."','".$dataAccount["mail"]."','".$dataAccount["auth"]."','".$dataAccount["md5"]."','".$dataAccount["date"]."','".$dataAccount["entry"]."')";
    $db->query($sql);

    $db->close();
  }
  public static function add_property_mysql($config,$dataProperty){
    $db = new \mysqli($config["host"] , $config["user"] , $config["pass"] , $config["database"]);
    if($db->connect_error){
      die("Error (code:mysql-002) No Database-Access.");
    }
    // property-tableチェック（無ければ作成）
    $connect_tb = \mysqli_query($db , "SHOW TABLES LIKE 'property'");
    if(!$connect_tb->num_rows){
      $sql_pr = "CREATE TABLE property (
        id VARCHAR(50) PRIMARY KEY,
        name VARCHAR(100),
        memo TEXT,
        date VARCHAR(14),
        entry VARCHAR(14)
      ) charset=utf8";
      $connect_pr = $db->query($sql_pr);
      if(!$connect_pr){
        die("Error (code:mysql-009) テーブル作成失敗[property]");
      }
    }

    // データ追加
    $sql  = "INSERT INTO property (id,name,memo,date,entry) VALUES ('".$dataProperty["id"]."','".$dataProperty["name"]."','".$dataProperty["memo"]."','".$dataProperty["date"]."','".$dataProperty["entry"]."')";
    $db->query($sql);

    $db->close();
  }
  public static function add_account_net($config,$dataAccount){

  }
  public static function add_property_net($config,$dataProperty){

  }
  public static function add_account_json($config,$dataAccount){
    // make account-dir
    $dir = self::$account_dir;
    if(!is_dir($dir)){
      mkdir($dir,0777,true);
    }
  }
  public static function add_property_json($config,$dataProperty){

  }


  // edit
  public static $cache_account = array();
  public static function getValue_account($id="" , $key=""){
    if($id === ""){return;}

    if(!isset(self::$cache_account[$id])){
      $res = \mynt\lib\data::load(
        "",
        "account",
        array(),
        array("id" => $id)
      );
      if($res["status"] === "error"){
        return "";
      }
      self::$cache_account[$id] = $res["data"][0];
    }

    if($key && isset(self::$cache_account[$id][$key])){
      return self::$cache_account[$id][$key];
    }
    else if(!$key && isset(self::$cache_account[$id])){
      return self::$cache_account[$id];
    }
    else{
      return "";
    }
  }

  

  // property
  public static $cache_property = array();
  public static function getValue_property($id="" , $key=""){
    if($id === ""){return;}

    if(!isset(self::$cache_property[$id])){
      $res = \mynt\lib\data::load(
        "",
        "property",
        array(),
        array("id" => $id)
      );
      if($res["status"] === "error"){
        return "";
      }
      self::$cache_property[$id] = $res["data"][0];
    }

    if($key && isset(self::$cache_property[$id][$key])){
      return self::$cache_property[$id][$key];
    }
    else if(!$key && isset(self::$cache_property[$id])){
      return self::$cache_property[$id];
    }
    else{
      return "";
    }
  }
  // public static function getValue_property($id="" , $key){
  //   if($id === "" || $key === ""){return;}
  //   $data_property = self::getData_property($id);
  //   if(isset($data_property[$key])){
  //     return $data_property[$key];
  //   }
  //   else{
  //     return "";
  //   }
  // }

  // // property-data
  // public static function getData_property($id=""){
  //   $config = \mynt\lib\data::getConfig();
  //   switch($config["type"]){
  //     case "mysql":
  //       return self::getData_property_mysql($config , $id);
  //       break;

  //     case "net":
  //       return self::getData_property_net($config , $id);
  //       break;

  //     default:
  //       return self::getData_property_json($config , $id);
  //       break;
  //   }
  //   return null;
  // }
  // public static function getData_property_mysql($config , $id=""){
  //   if(!$id){return;}
  //   $db = new \mysqli($config["host"] , $config["user"] , $config["pass"] , $config["database"]);
  //   $sql = "SELECT id,name,memo,entry FROM property WHERE id = '".$id."'";
  //   $datas = $db->query($sql);
  //   if(!$datas){return null;}
  //   while($row = mysqli_fetch_assoc($datas)){
  //     return $row;
  //   }
  //   return null;
  // }
  // public static function getData_property_net($config , $id=""){

  // }
  // public static function getData_property_json($config , $id=""){
  //   if($id === ""){return;}
  //   $dir  = $config["dir"];
  //   $file = "login.json";
  //   if(is_file($dir.$file)){
  //     $datas = explode(PHP_EOL,file_get_contents($dir.$file));
  //     for($i=count($datas)-1; $i>=0; $i--){
  //       $datas[$i] = str_replace("\r","",$datas[$i]);
  //       $datas[$i] = str_replace("\n","",$datas[$i]);
  //       if(!$datas[$i]){continue;}
  //       $json = json_decode($datas[$i] , true);
  //       if(isset($json["flg"]) && $json["flg"] == 1){continue;}
  //       if(isset($json["id"]) && $json["id"] === $id){
  //         return $json;
  //       }
  //     }
  //   }
  //   return;
  // }


  // Auth

  public static $authPath = "mynt/config/auth.json";

  public static function getAuthLists(){
    if(!is_file(self::$authPath)){return;}
    return json_decode(file_get_contents(self::$authPath),true);
    // $datas = json_decode(file_get_contents(self::$authPath),true);
    // $res   = array();
    // for($i=0; $i<count($datas); $i++){
    //   if(!$datas[$i]){continue;}
    //   array_push($res , $datas[$i]);
    // }
    // return $res;
  }

  // authリストのselect-option出力
  public static function viewAuth_options($currentAuthId=""){
    $datas = self::getAuthLists();
    if(!$datas){return;}
    $html = "";
    for($i=0; $i<count($datas); $i++){
      if(!$datas[$i]){continue;}
      $selected = ($datas[$i]["id"] === $currentAuthId) ? "selected" : "";
      $html .= "<option value='".$datas[$i]["id"]."' ".$selected.">".$datas[$i]["name"]."</option>".PHP_EOL;
    }
    return $html;
  }

  // authリストのselect-option出力
  public static function viewAuth_options_select($id=""){
    if(!$id){
      return $authLists = self::viewAuth_options();
    }
    $data = self::getValue_account($id);
// error_log("id : ".$id);
// error_log($data);
    return $authLists = self::viewAuth_options($data["auth"]);
    // $datas = self::getAuthLists();
    // $html = null;
    // for($i=0; $i<count($datas); $i++){
    //   if(!$datas[$i]){continue;}
    //   $selected = ($data["auth"] === $datas[$i]["id"]) ? "selected" : "";
    //   $html .= "<option value='".$datas[$i]["id"]."' ".$selected.">".$datas[$i]["name"]."</option>".PHP_EOL;
    // }
    // return $html;
  }

  




}