<?php
/**
 * Path    : lib/_mynt.php
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.4.0
 * Summary : myntstudioフレームワークで必ず初回にincludeされる特殊ライブラリPHP
 * Example : 
 */

// require_once __DIR__."/init.php";


class mynt{

  // System =====

  // Load-PHP-Module
	public static function loadPHPs($dir=""){
    // check-dir
    if($dir==="" || !is_dir($dir)){return;}

    // check-directory-last-string
    if(!preg_match("/\/$/",$dir)){
      $dir .= "/";
    }

    // load-directory-inner-files
    $files = scandir($dir);
    for($i=0; $i<count($files); $i++){
      if($files[$i] == "." || $files[$i] == ".." || !preg_match("/\.php$/",$files[$i])){continue;}

      $path = $dir.$files[$i];

      // check-class-exist
      $class = str_replace(".php" , "" , $path);
      $class = str_replace("/","\\",$class);
      if(class_exists($class)){continue;}
      require_once $dir.$files[$i];
    }
   }
  

  // Method =====

	// HTMLからfunction呼び出しの為の処理Check Method *POST only
	public static function method(){
		// referer-security
    if(!self::checkReferer()){return;}
    
    // if($_REQUEST["a"]){
    //   die("php : ".$_REQUEST["php"]."/".$_REQUEST["b"]);
    // }

		// method [ class / function ]
		if(isset($_POST["method"]) && $_POST["method"]){
			$sp = explode("::",$_POST["method"]);
			if(count($sp) === 2 && method_exists($sp[0],$sp[1])){
				call_user_func_array(array($sp[0] , $sp[1]) , array());
			}
		}
		else if(isset($_POST["method_return"]) && $_POST["method_return"]){
			$sp = explode("::",$_POST["method_return"]);
			if(count($sp) === 2 && method_exists($sp[0],$sp[1])){
				echo call_user_func_array(array($sp[0] , $sp[1]) , array());
			}
			exit();
    }
    else if(isset($_POST["php"]) && $_POST["php"]){
      $ptn = "/^(.*?)\:\:(.*?)\((.*?)\)$/is";
      preg_match($ptn , $_POST["php"] , $match);
      $query = ($match[3]) ? explode(",",$match[3]) : array();
      for($i=0; $i<count($query); $i++){
        $query[$i] = str_replace(array("'",'"'),"" , $query[$i]);
      }
			if($match[1] && $match[2] && !class_exists($match[1])){
        self::checkModuleLoad($match[1]);
      }
      if(method_exists($match[1],$match[2])){
        echo call_user_func_array(array($match[1] , $match[2]) , $query);
      }
      if($_POST["exit"] == "true"){
        exit();
      }
    }
    
	}

	// referer-check
	public static function checkReferer(){return true;
		if(!isset($_SERVER['HTTP_REFERER']) || !$_SERVER['HTTP_REFERER']){
			return false;
		}
		// リファラが同じサーバー発信でなければNG
		$info_referer = parse_url($_SERVER['HTTP_REFERER']);
		if($_SERVER['HTTP_HOST'] === $info_referer["host"]){
			return true;
		}
		else{
			return false;
		}
	}

  // Common Modules =====

  public static function currentTime(){
		return date("YmdHis");
	}
	public static function getTime(){
		return time();
  }
  
  // page読み込みの際に、pクエリと同名のcss,jsの読み込みタグをセットする（headタグ内に記載）
	public static function setPageModules($page=""){return "";
		if(!$page){return;}

		// if(isset($GLOBALS["config"]["default"]["login-top"]) && $GLOBALS["config"]["default"]["login-top"]){
		// 	$p = str_replace(".html","",$GLOBALS["config"]["default"]["login-top"]);
		// }
		// else if(!$p && isset($GLOBALS["config"]["default"]["path-top"]) && $GLOBALS["config"]["default"]["path-top"]){
		// 	$p = str_replace(".html","",$GLOBALS["config"]["default"]["path-top"]);
		// }

		$service = $GLOBALS["config"]["service"]["path"];
    if(!$service){return;}
    
    $html = "";

    // css
		$css_path = "mynt/".$service."/css/".$page.".css";
		if(is_file($css_path)){
			$html .= "<link rel='stylesheet' href='".$css_path."?".$GLOBALS["config"]["default"]["version"]."'>";
    }
    
		// js
		$js_path = "myns/".$service."/js/".$p.".js";
		if(is_file($js_path)){
			$html .= "<script src='".$js_path."?".$GLOBALS["config"]["default"]["version"]."'></script>";
		}
		return $html;
	}
	
	// Modules =====
	// check module-load : クラスの読み込み確認、読み込まれていないクラスはrequireして再度チェックする
  public static function checkModuleLoad($class=""){
    if(!$class){return false;}
    if(class_exists($class)){return true;}
    $sp   = explode("\\" , $class);
    $sp1  = array_filter(array_slice($sp , 0 , -1));
    $sp2  = array_slice($sp , -1 , 1);
    $path =  implode("/",$sp1) . "/php/". implode("/",$sp2) .".php";
    if(!is_file($path)){return false;}
		require_once $path;
		if(class_exists($class)){
			return true;
		}
		else{
			return false;
		}
  }

  public static function exec($class="" , $function="" , $query=array()){
    return self::execution($class,$function,$query);
  }
  public static function data_save($query=array()){
    return self::execution("\\mynt\\lib\\data" , "save" , $query);
  }
  public static function data_load($query=array()){
    return self::execution("\\mynt\\lib\\data" , "load" , $query);
  }
  public static function data_del($query=array()){
    return self::execution("\\mynt\\lib\\data" , "del" , $query);
  }

  public static function execution($class="" , $function="" , $query=array()){
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
    if(method_exists($class,$function)){
      return call_user_func_array(array($class , $function) ,$query);
    }

    return false;
  }

  public static function classProperty($class="" , $prop=""){
    // check-class (& require)
    if(!self::checkModuleLoad($class)){
      return;
    }

    // get-string
    $classData = get_class_vars($class);
    if(isset($classData[$prop])){
      return $classData[$prop];
    }
    else{
      return null;
    }
  }

  // ###.phpの###部分を彫る
  // ex) {{php:\mynt::getBasename()}}
  public static $cacheBasename = null;
  public static function getBasename(){
    if(self::$cacheBasename === null){
      if(isset($argv)){
        $script = str_replace(".php","",$argv[0]);
      }
      else if(isset($_SERVER["argv"])){
        $script = str_replace(".php","",$_SERVER["argv"][0]);
      }
      else{
        $script = str_replace(".php","",basename(self::exec("\\mynt\\lib\\url","getUrl")));
      }
      self::$cacheBasename = $script;
    }
    return self::$cacheBasename;
  }


  /**
	* Contents
	* 1. ? blog=** / default=** / system=** / etc=**
	* 2. ?b=**&p=** (data/page/base/page.html)
	*/
	public static function contents($page="",$dir=""){
		return self::execution("\\mynt\\lib\\design" , "contents" , array($page,$dir));
  }

  // ログインしていなければリダイレクトするチェックを行う（認証必須ページ用）
	public static function checkLogin($path=""){
		// signin
		if(!isset($_SESSION["login_id"]) || !$_SESSION["login_id"]){
			self::execution("\\mynt\\lib\\url","setUrl",array($path));
		}
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
  
  
  // Error
  function error($str=""){
    die($str);
  }

}