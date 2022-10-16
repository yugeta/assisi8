<?php
// namespace api;
$root = dirname(__FILE__);
require_once $root . "/lib/main/init.php";
class mynt extends \lib\main\init {}
// require_once "lib/html/php.php";


// 受け渡し用変数
$GLOBALS["__dir__"] = $root;



/**
 * - issue
 *   api/%module-name%/api.php
 * 
 * Sample : 
 * $ php api.php path=./page/crawl/genre/pharmacy/kouseikyoku.php php=\\page\\crawl\\genre\\pharmacy\\kouseikyoku::init\(aa,bb\)
 * $ php api.php module=fax
 * 
 * MYNT_API : 
 * <script src="https://api.mynt.site/api.php?module=mynt_api&...">
 */

 $querys = \common::query_convert();
// print_r($querys);
// $querys = array();
// if(isset($argv) && $argv){
//   foreach($argv as $query){
//     $sp = explode("=",$query);
//     if(count($sp) !== 2){continue;}
//     $querys[$sp[0]] = $sp[1];
//   }
// }
// else if(isset($_REQUEST)){
//   foreach($_REQUEST as $key => $val){
//     $querys[$key] = $val;
//   }
// }


$res = common::load($querys);
if(!$res){
  print_r($querys);
  exit("-- api --");
}
// else{
//   exit("finish");
// }



class common{
  public static function load($options=array()){
    if(!$options){return;}
    $root = dirname(__FILE__);

    // api-module指定
    if(isset($options["module"]) && $options["module"] && is_file($root."/api/".$options["module"]."/api.php")){
      // $GLOBALS["querys"] = $options;
      // print_r($GLOBALS);
// die($root."/api/".$options["module"]."/api.php");
      require_once $root."/api/".$options["module"]."/api.php";
      return true;
    }

    // php-path指定
    else if(isset($options["path"]) && $options["path"] && is_file($options["path"])){
      // require
      $GLOBALS["options"] = $options;
      require_once $GLOBALS["options"]["path"];

      // 実行
      if(!isset($options["php"]) || !$options["php"]){return;}
      $procs = self::disassembly($options["php"]);
      if(!$procs){return;}
      \mynt::exec($procs["class"] , $procs["function"] , array($procs["value"]));
      return true;
    }
  }

  // query -> proc-data
  public static function disassembly($php=""){
    if(!$php){return;}
		$ptn = '(.+?)\:\:(.+?)\((.*?)\)';
		if(preg_match("/".$ptn."/is" , $php  , $match)){
      $value = isset($match[3]) ? str_getcsv($match[3]) : "";
      return array(
        "class"    => $match[1],
        "function" => $match[2],
        "value"    => $value
      );
    }
    return null;
  }


  // // ----------
  // // lib/main/init.phpよりコピー

  // // 任意の関数を実行
  // public static function exec($class="" , $function="" , $query=array()){//echo $class.$function.PHP_EOL;

  //   if(!$class || !$function){return;}

  //   // query-set
  //   if(gettype($query) !== "array"){
  //     $query = array($query);
  //   }

  //   // check-class (& require)
  //   if(!self::checkModuleLoad($class)){
  //     return;
  //   }

  //   // start-function
  //   if(method_exists($class,$function)){
  //     return call_user_func_array(array($class , $function) ,$query);
  //   }

  //   return false;
  // }
  // // Modules =====
	// // check module-load : クラスの読み込み確認、読み込まれていないクラスはrequireして再度チェックする
  // public static function checkModuleLoad($class=""){
  //   if(!$class){return false;}
  //   if(class_exists($class)){return true;}
  //   $sp   = explode("\\" , $class);
  //   $sp1  = array_filter(array_slice($sp , 0 , -1));
  //   $sp2  = array_slice($sp , -1 , 1);
  //   $path =  join("/",$sp1) . "/". join("/",$sp2) .".php";
  //   if(!is_file($path)){return false;}
	// 	require_once $path;
	// 	if(class_exists($class)){
	// 		return true;
	// 	}
	// 	else{
	// 		return false;
	// 	}
  // }

  public static function query_convert(){
// print_r($_SERVER);
// print_r($argv);
    $querys = array();

    if(isset($argv) && $argv){
      foreach($argv as $query){
        $sp = explode("=",$query);
        // if(count($sp) !== 2){continue;}
        $querys[$sp[0]] = implode("=",array_slice($sp,1));
      }
    }

    // mac-php用
    if(isset($_SERVER["argv"]) && $_SERVER["argv"]){
      foreach($_SERVER["argv"] as $query){
        $sp = explode("=",$query);
        // if(count($sp) !== 2){continue;}
        // $querys[$sp[0]] = $sp[1];
        $querys[$sp[0]] = implode("=",array_slice($sp,1));
      }
    }

// print_r($argv);
// print_r($querys);exit();

    if(isset($_POST)){
      foreach($_POST as $key => $val){
        $querys[$key] = $val;
      }
    }

    // xml-url対応
    if(isset($_SERVER["QUERY_STRING"])){
      if(strstr($_SERVER["QUERY_STRING"] , "&amp;")){
        $arr = explode("&amp;" , $_SERVER["QUERY_STRING"]);
      }
      else{
        $arr = explode("&" , $_SERVER["QUERY_STRING"]);
      }
      for($i=0; $i<count($arr); $i++){
        $sp = explode("=" , $arr[$i]);
        $_REQUEST[$sp[0]] = $_GET[$sp[0]] = $querys[$sp[0]] = implode("=",array_slice($sp,1));
      }
    }
// print_r($querys);exit();
    return $querys;
  }
}

