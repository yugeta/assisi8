<?php
namespace lib\install;

class data{


  public static function init_save(){
    
    if(!isset($_POST["account"])){return;}


    // validation
    $_REQUEST["message"] = "";

    if(!$_POST["account"]["password"]){
      $_REQUEST["message"] .= "<li>「パスワード」が未入力です。</li>";
    }
    else if($_POST["account"]["password"] !== $_POST["account"]["password2"]){
      $_REQUEST["message"] .= "<li>「パスワード確認」が違います。</li>";
    }

    $mail_exp = "|^[0-9a-z_./?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$|";
    if(!$_POST["account"]["mail"]){
      $_REQUEST["message"] .= "<li>「メールアドレス」が未入力です。</li>";
		}
		else if(!preg_match($mail_exp , $_POST["account"]["mail"])){
      $_REQUEST["message"] .= "<li>「メールアドレス」が間違っています。</li>";
		}

    if($_REQUEST["message"] !== ""){
      $_REQUEST["message"] = "<ol>".$_REQUEST["message"]."</ol>";
      return false;
    }



    $id = isset($_POST["account"]["id"]) ? $_POST["account"]["id"] : "";
    $id = $id ? $id : \mynt::exec("\lib\auth\common" , "makeAccountId");
    $update = date("YmdHis");

    $datas_account = array(
      "id"     => $id,
      "mail"   => $_POST["account"]["mail"],
      "auth"   => $_POST["account"]["auth"],
      "md5"    => md5($_POST["account"]["password"]),
      "entry"  => $update
    );
    
    $data_property = array(
      "id"     => $id,
      "name"   => $_POST["property"]["name"],
      "memo"   => $_POST["property"]["memo"],
      "entry"  => $update
    );



    if(isset($_REQUEST["redirect"]) && $_REQUEST["redirect"]){
      \mynt::exec("\\mynt\\lib\\url","setUrl",array($_REQUEST["redirect"]));
    }
    else{
      $url = \mynt::exec("\\mynt\\lib\\url","getUrl",array());
      \mynt::exec("\\mynt\\lib\\url","setUrl",array($url));
    }
    exit("Redirected...");
  }

  public static function check_mail($str=""){

  }

  public static function check_pass($str=""){
    if($str && strlen($str) >= 6){
      return true;
    }
    else{
      return false;
    }
  }




  // data環境設定
  public static $config_data = null;
  public static function getData(){
    if(self::$config_data !== null){return self::$config_data;}
    $path = "mynt/config/data.json";
    if(!is_file($path)){return;}
    $text = file_get_contents($path);
    $config = json_decode($text , true);

    $dbname = (isset($config["database"])) ? $config["database"] : "";
    $path2 = "mynt/config/tables/".$dbname.".json";
    if($dbname && is_file($path2)){
      $db2 = json_decode(file_get_contents($path2) , true);
      if($db2){
        $config["tables"] = array_merge($config["tables"] , $db2);
      }
    }


    self::$config_data = $config;
    return self::$config_data;
  }

  // Load-Config
	public static function load($pref=""){

    $config = self::getData();

    $data = array();
    $type = \lib\main\common::exec("\\mynt\\lib\\data","getType",array());
    switch($type){
      case "mysql":
        $mysql = \mysqli_connect($config["host"] , $config["user"] , $config["pass"] , $config["database"]);
        $sql = "SELECT type,name,value FROM config";
        $ret = $mysql->query($sql);
        while($row = mysqli_fetch_assoc($ret)){
          if(!isset($data[$row["type"]])){
            $data[$row["type"]] = array();
          }
          $data[$row["type"]][$row["name"]] = $row["value"];
        }
        break;

      case "net":
        $data = \lib\main\common::exec("\\mynt\\lib\\data_net" , "loadConfig" , array($data_config));
        break;

      default : // json...
        if(!isset($config["dir"])
        || !isset($config["database"])
        || !is_dir($config["dir"].$config["database"])
        ){
          die("Error (code:json-001) Config[dir] not-directory.");
        }
        $path = $config["dir"].$config["database"]."/"."config.json";
        if(!is_file($path)){return;}
        $data  = array();
        $lines = explode("\n",file_get_contents($path));
        $cache = array();
        for($i=count($lines)-1; $i>=0; $i--){
          if(!$lines[$i]){continue;}
          $json = json_decode($lines[$i],true);
          if(!isset($json["id"]) || !$json["id"]){continue;}
          if(isset($cache[$json["id"]])){continue;}
          $cache[$json["id"]] = true;
          if(!isset($data[$json["type"]])){
            $data[$json["type"]] = array();
          }
          $data[$json["type"]][$json["name"]] = $json["value"];
        }
        break;
    }

    if(!$data){
      die("Error (code:data-003) configデータがありません。");
    }

    // data-design adjust
    if(isset($data["design"])){
      $data["design"]["path"] = "mynt/design/" . $pref ."/";
    }

    $GLOBALS["config"] = $data;
		return true;
  }
  

  // config情報のメモリセット
	public static function setConfig(){
    $data = self::load();
		if(!$data){return;}

    $GLOBALS["config"]  = $data;

    // check-design
    if(!isset($GLOBALS["config"]["design"])){
      $GLOBALS["config"]["design"] = array();
    }
    if(!isset($GLOBALS["config"]["design"]["target"])
    || !$GLOBALS["config"]["design"]["target"]){
      $GLOBALS["config"]["design"]["target"] = "sample";
    }
    $dir_design = \lib\main\common::classProperty("\\mynt\\lib\\design","dir");
    $GLOBALS["config"]["design"]["path"] = $dir_design . $GLOBALS["config"]["design"]["target"]."/";

    // check-service
    if(!isset($GLOBALS["config"]["service"])){
      $GLOBALS["config"]["service"] = array();
    }
    if(!isset($GLOBALS["config"]["service"]["target"])
    || !$GLOBALS["config"]["service"]["target"]){
      $GLOBALS["config"]["service"]["target"] = "sample";
    }
    $dir_service = \lib\main\common::classProperty("\\mynt\\lib\\service","dir");
    $GLOBALS["config"]["service"]["path"] = $dir_service . $GLOBALS["config"]["service"]["target"]."/";

		// 初回設定データチェック
    if(!\lib\main\common::exec("\\mynt\\lib\\data" , "checkConfig" , array())){
			die("Error : no-config-data.");
		}

    return true;
  }
}