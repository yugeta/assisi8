<?php
namespace lib\main;

// language
putenv("LANG=ja_JP.UTF-8");
ini_set('default_charset', 'UTF-8');

// timezone
date_default_timezone_set('Asia/Tokyo');

// session
ini_set("set_time_limit"          , 240);    // 
ini_set("memory_limit"            , "1000M"); // ダウンロードするデータの上限ファイルサイズ
ini_set('session.gc_maxlifetime'  , 604800); //セッションtime 秒(デフォルト:1440)-> 1day:86400 1week:604800 30day:2592000
ini_set('session.gc_probability'  , 1);      // 分子(デフォルト:1)
ini_set('session.gc_divisor'      , 100);    // 分母(デフォルト:100)
ini_set('session.use_only_cookies', 1);      // [0:ブラウザを閉じるまで有効 , 1:ブラウザを閉じても有効]




class init{
  
  // Modules =====
	// check module-load : クラスの読み込み確認、読み込まれていないクラスはrequireして再度チェックする
  public static function checkModuleLoad($class=""){
    if(!$class){return false;}
    if(class_exists(self::class_name_replace($class))){return true;}
    $sp   = explode("\\" , $class);
    $sp1  = array_filter(array_slice($sp , 0 , -1));
    $sp2  = array_slice($sp , -1 , 1);
    $path =  implode("/",$sp1) . "/". implode("/",$sp2) .".php";
    if(!is_file($path)){return false;}
		require_once $path;
		if(class_exists(self::class_name_replace($class))){
			return true;
		}
		else{
			return false;
		}
  }

  // 任意の関数を実行
  public static function exec($class="" , $function="" , $query=array()){

    if(!$class || !$function){return;}

    // query-set
    if(gettype($query) !== "array"){
      $query = array($query);
    }

    // check-class (& require)
    if(!self::checkModuleLoad($class)){
      return;
    }

    // start-function
    $class_2 = self::class_name_replace($class); // 禁止文字対応.と-を_に変換する。
    if(method_exists($class_2,$function)){
      return call_user_func_array(array($class_2 , $function) ,$query);
    }

    return false;
  }

  // 任意のclass内のローカル変数を取得
  public static function value($class="" , $variable=""){
    if(!$class || !$variable){return;}

    // check-class (& require)
    if(!self::checkModuleLoad($class)){
      return;
    }

    // get-variable
    if(isset($class::${$variable})){
      return $class::${$variable};
    }

    return;
  }
  


  // HTMLからfunction呼び出しの為の処理Check Method *POST only
	public static function method(){
		// referer-security
    if(!\mynt::exec('\lib\common\referer',"check")){return;}

		// method [ class / function ]
		if(isset($_POST["method"]) && $_POST["method"]){
			$sp = explode("::",$_POST["method"]);
      $class_name = $sp[0];
      $class_name = self::class_name_replace($class_name);
			if(count($sp) === 2 && method_exists($class_name , $sp[1])){
				call_user_func_array(array($class_name , $sp[1]) , array());
			}
		}
    // method_return
		else if(isset($_POST["method_return"]) && $_POST["method_return"]){
			$sp = explode("::",$_POST["method_return"]);
      $class_name = self::class_name_replace($sp[0]);
			if(count($sp) === 2 && method_exists($class_name , $sp[1])){
				echo call_user_func_array(array($class_name , $sp[1]) , array());
			}
			exit();
    }
    else if(isset($_POST["php"]) && $_POST["php"]){
      $ptn = "/^(.*?)\:\:(.*?)\((.*?)\)$/is";
      preg_match($ptn , $_POST["php"] , $match);
      $class_name = $match[1];
      $class_name = self::class_name_replace($class_name);
      $query = \mynt::exec('\lib\html\replace','changeQueryArray',array($match[3]));
			if($class_name && $match[2] && !class_exists($class_name)){
        self::checkModuleLoad($match[1]);
      }
      if(method_exists($class_name , $match[2])){
        echo call_user_func_array(array($class_name , $match[2]) , $query);
      }
      if(isset($_POST["exit"]) && $_POST["exit"] == "true"){
        exit();
      }
    }
  }

  public static function class_name_replace($class_name=""){
    if($class_name===""){return $class_name;}
    $class_name = str_replace("." , "_" , $class_name);
    $class_name = str_replace("-" , "_" , $class_name);
    return $class_name;
  }

  // $_GET["q"]のページ読み込み時の分解処理
  // return @ [変換した個数（クエリ数）]
  public static function queryParameters(){
    return self::exec('\lib\url\query','param_check');
  }


  // data
  public static function data_save($query=array()){
    return self::exec("\\lib\\data\\data" , "save" , func_get_args());
  }
  public static function data_load($query=array()){
    return self::exec("\\lib\\data\\data" , "load" , func_get_args());
  }
  public static function data_del($query=array()){
    return self::exec("\\lib\\data\\data" , "del" , func_get_args());
  }
  

  // view
  public static function page($query=array()){
    echo \mynt::exec('\lib\html\page' , 'view' , func_get_args());
  }

  // time
  public static function currentTime(){
		return date("YmdHis");
  }
  
  // copuright用年取得
  public static function getYear(){
    return date("Y");
  }

  // browser-id
  public static function session_id(){
    if(!isset($_SESSION)){return;}
    return session_id();
  }
  

}

// use \lib\main\common as mynt;