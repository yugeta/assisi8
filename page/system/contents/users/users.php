<?php
namespace page\system\contents\users;

class users{


  // view - lists
  public static function getLists_tr(){
    $lists = self::getLists();

    $auth_db = \mynt::data_load("","lib_auth");
    $auth_val = array();
    if($auth_db["status"] === "ok"){
      foreach($auth_db["data"] as $num => $auth){
        $auth_val[$auth["id"]] = $auth["name"];
      }
    }

    $html ="";
    for($i=0; $i<count($lists); $i++){
      $id    = (isset($lists[$i]["id"]))    ? $lists[$i]["id"]    : "";
      $mail  = (isset($lists[$i]["mail"]))  ? $lists[$i]["mail"]  : "";
      $auth  = (isset($lists[$i]["auth"]))  ? $lists[$i]["auth"]  : "";
      $name  = (isset($lists[$i]["name"]))  ? $lists[$i]["name"]  : "";
      $memo  = (isset($lists[$i]["memo"]))  ? $lists[$i]["memo"]  : "";
      $entry = (isset($lists[$i]["entry"])) ? $lists[$i]["entry"] : "";

      $html .= "<tr data-id='".$id."'>";
      // $html .= "<th class='num'>".($i+1)."</th>";
      $html .= "<th class='num'></th>";
      // $html .= "<td class='id'>".$id."</td>";
      $html .= "<td class='mail'>".$mail."</td>";
      $html .= "<td class='auth'>". (isset($auth_val[$auth]) ? $auth_val[$auth] : $auth) ."</td>";

      $html .= "<td class='name'>".$name."</td>";
      $html .= "<td class='comment'>".str_replace("\n","<br>",$memo)."</td>";
      // $html .= "<td class='entry'>".$entry."</td>";
      $html .= "</tr>";
    }
    return $html;
  }

  // view - lists
  public static function getLists_option($currentValue=""){
    $lists = self::getLists();

    // $auth_db = \mynt::data_load("","lib_auth");
    // $auth_val = array();
    // if($auth_db["status"] === "ok"){
    //   foreach($auth_db["data"] as $num => $auth){
    //     $auth_val[$auth["id"]] = $auth["name"];
    //   }
    // }

    $html ="";
    for($i=0; $i<count($lists); $i++){
      $id    = (isset($lists[$i]["id"]))    ? $lists[$i]["id"]    : "";
      $mail  = (isset($lists[$i]["mail"]))  ? $lists[$i]["mail"]  : "";
      $auth  = (isset($lists[$i]["auth"]))  ? $lists[$i]["auth"]  : "";
      $name  = (isset($lists[$i]["name"]))  ? $lists[$i]["name"]  : "";
      $memo  = (isset($lists[$i]["memo"]))  ? $lists[$i]["memo"]  : "";
      $entry = (isset($lists[$i]["entry"])) ? $lists[$i]["entry"] : "";

      $html .= "<option value='".$id."'>";
      $html .= $name ? $name : $mail;
      $html .= "</option>";
      // $html .= "<tr data-id='".$id."'>";
      // $html .= "<th class='num'>".($i+1)."</th>";
      // // $html .= "<td class='id'>".$id."</td>";
      // $html .= "<td class='mail'>".$mail."</td>";
      // $html .= "<td class='auth'>". (isset($auth_val[$auth]) ? $auth_val[$auth] : $auth) ."</td>";

      // $html .= "<td class='name'>".$name."</td>";
      // $html .= "<td class='comment'>".str_replace("\n","<br>",$memo)."</td>";
      // // $html .= "<td class='entry'>".$entry."</td>";
      // $html .= "</tr>";
    }
    return $html;
  }

  public static function getLists(){
    // $account  = \mynt::exec('\lib\data\data','data_load',array('','lib_account',[],[],["id"=>"SORT_ASC"]));
    // $property = \mynt::exec('\lib\data\data','data_load',array('','lib_property',[],[],[]));
    $account  = \mynt::data_load('','lib_account',[],[],["id"=>"SORT_ASC"]);
    $property = \mynt::data_load('','lib_property');

    $res = array();
    if($account["status"] === "ok"){
      foreach($account["data"] as $num => $data){
        $id = $data["id"];
        $res[$id] = $data;
      }
    }
    if($property["status"] === "ok"){
      foreach($property["data"] as $num => $data){
        $id = $data["id"];
        $res[$id]["name"] = ($data["name"]) ? $data["name"] : "";
        $res[$id]["memo"] = ($data["memo"]) ? $data["memo"] : "";
      }
    }
    
    $datas    = array();
    foreach($res as $value){
      array_push($datas , $value);
    }

    return $datas;
  }


  // add =====

  // // id
  // public static function makeAccountId(){
  //   return self::getMsTime().".".rand(0,999);
  // }
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

    $where0 = array(
      "mail" => $_POST["account"]["mail"]
    );
    $res = \mynt::data_load("" , "lib_account" , array() , $where0);
    if($res["status"] === "ok"){
      $_REQUEST["errorMessage"] .= "<li>登録済みのメールアドレスです。</li>";
    }

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

    // $id = ($_POST["account"]["id"]) ? $_POST["account"]["id"] : self::makeAccountId();

    $passwd = \mynt::exec('\lib\auth\password','encode',array($_POST["account"]["password"]));
    $dataAccount = array(
      // "id"     => $id,
      "mail"   => $_POST["account"]["mail"],
      "auth"   => $_POST["account"]["auth"],
      "pass"   => $passwd,
      "date"   => $update,
      "entry"  => $update
    );
    $where = array();
    if($_POST["account"]["id"]){
      $dataAccount["id"] = $_POST["account"]["id"];
      $where = array("id"=>$_POST["account"]["id"]);
    }
    $res = \mynt::data_save("" , "lib_account" , $dataAccount , $where);
    if($res["status"] === "error"){
      die("Error データが正常に追加できません。");
    }

    $id = $res["data"]["id"];

    if($_POST["property"]["name"] && $_POST["property"]["memo"]){
      $dataProperty = array(
        "id"     => $id,
        "name"   => $_POST["property"]["name"],
        "memo"   => $_POST["property"]["memo"],
        "date"   => $update,
        "entry"  => $update
      );
      $res2 = \mynt::data_save("","lib_property" , $dataProperty , array());
      if($res2["status"] === "error"){
        die("Error データが正常に追加できません。");
      }
    }

    if(isset($_REQUEST["redirect"]) && $_REQUEST["redirect"]){
      \mynt::exec('\lib\common\url',"setUrl",array($_REQUEST["redirect"]));
    }
    else{
      $url = \mynt::exec('\lib\common\url',"getUrl",array());
      \mynt::exec('\lib\common\url',"setUrl",array($url));
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
    $accountRes  = \mynt::data_load("" , "lib_account"  , array()  , $where);
    $propertyRes = \mynt::data_load("" , "lib_property"  , array()  , $where);
    if($accountRes["status"] === "error"){
      die("登録されていないアカウントです。");
      // \mynt::exec("\\mynt\\lib\\error" , "view" , array("登録されていないアカウントです。"));
    }

    
    // account
    $dataAccount =array(
      "mail" => $_POST["account"]["mail"],
      "auth" => $_POST["account"]["auth"],
      "date" => $update
    );

    // password 更新登録
    if($_POST["account"]["password"] !== ""){
      $dataAccount["pass"] = \mynt::exec('\lib\auth\password','encode',array($_POST["account"]["password"]));
    }
    // 既存パスワード読み込み
    else{
      $dataAccount["pass"] = $accountRes["data"][0]["pass"];
    }

    if($propertyRes["status"] === "error"){
      $dataProperty =array(
        "id"    => $_POST["account"]["id"],
        "name"  => $_POST["property"]["name"],
        "memo"  => $_POST["property"]["memo"],
        "entry" => $update
      );
    }
    else{
      $dataProperty =array(
        "name" => $_POST["property"]["name"],
        "memo" => $_POST["property"]["memo"],
        "entry" => $update
      );
    }

    // save
    $res = \mynt::data_save("" , "lib_account"  , $dataAccount  , $where);
    if($res["status"] === "error"){
      die("Error データが正常に修正できません。");
    }

    $res = \mynt::data_save("" , "lib_property" , $dataProperty , $where);
    if($res["status"] === "error"){
      die("Error データが正常に修正できません。");
    }


    // redirect
    if(isset($_REQUEST["redirect"]) && $_REQUEST["redirect"]){
      \mynt::exec('\lib\common\url','setUrl',array($_REQUEST["redirect"]));
    }
    else{
      $url = \mynt::exec('\lib\common\url',"getUrl",array());
      \mynt::exec('\lib\common\url','setUrl',array($url));
    }
    exit("redirected...");
  }

  public static function request_del(){
    $date = date("YmdHis");

    // account
    $dataAccount = array(
      "date" => $date
    );

    // property
    $dataProperty = array(
      "date" => $date
    );

    $wheres = array(
      "id" => $_REQUEST["id"]
    );

    $res = \mynt::data_del("" , "lib_account"  , $dataAccount  , $wheres);
    if($res["status"] === "error"){
      die("Error データが正常に削除できません。");
    }
    $res = \mynt::data_del("" , "lib_property" , $dataProperty , $wheres);
    if($res["status"] === "error"){
      die("Error データが正常に削除できません。");
    }

    // redirect
    if(isset($_REQUEST["redirect"]) && $_REQUEST["redirect"]){
      \mynt::exec('\lib\common\url',"setUrl",array($_REQUEST["redirect"]));
    }
    else{
      $url = "?p=system&c=users";
      \mynt::exec('\lib\common\url',"setUrl",array($url));
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
      $res = \mynt::data_load(
        "",
        "lib_account",
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
    else if(isset(self::$cache_account[$id])){
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
      $res = \mynt::data_load(
        "",
        "lib_property",
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
    else if(isset(self::$cache_property[$id])){
      return self::$cache_property[$id];
    }
    else{
      return "";
    }
  }
  


  // Auth

  // authリストのselect-option出力
  public static function viewAuth_options($currentAuthId=""){
    // $datas = self::getAuthLists();
    $auths = \mynt::data_load(
      "",
      "lib_auth",
      array(),
      array(),
      array("rank"=>"SORT_ASC")
    );
    if(!$auths || $auths["status"] === "error"){return;}

    $html = "";
    foreach($auths["data"] as $num => $data){
      $selected = ($currentAuthId && $currentAuthId == $data["id"]) ? "selected" : "";
      $html .= "<option value='".$data["id"]."' ".$selected.">".$data["name"]."</option>".PHP_EOL;
    }
    return $html;
  }

  // authリストのselect-option出力
  public static function viewAuth_options_select($id=""){
    $val = ($id) ? self::getValue_account($id,"auth") : "";
    $authLists = self::viewAuth_options($val);
    return $authLists;
  }
}
