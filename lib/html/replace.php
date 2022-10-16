<?php
namespace lib\html;
/**
 * Path    : lib/php/tag.php
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.1.0
 * Summary : HTMLタグに記述するプログラム処理 Replacement-Tag(RepTag) mynt-format
 * Example : {{method:\mynt\lib\tag::test("value")}}
 */

class replace{

	// 基本括弧文字列
	public static $tags = array("\{\{","\}\}");

	public static function conv($source=""){
    if($source===""){return;}
    return self::pattern($source);
  }

	public static function pattern($source){

		// $source = \mynt::exec("\\lib\\html\\query::check('".$source."','".self::$tags."')");

		$source = \mynt::exec("\\lib\\html\\query"   , "check" , array($source , self::$tags));
		$source = \mynt::exec("\\lib\\html\\method"  , "check" , array($source , self::$tags));
		$source = \mynt::exec("\\lib\\html\\php"     , "check" , array($source , self::$tags));
		$source = \mynt::exec("\\lib\\html\\str"     , "check" , array($source , self::$tags));
		$source = \mynt::exec("\\lib\\html\\file"    , "check" , array($source , self::$tags));
		$source = \mynt::exec("\\lib\\html\\ptn_if"  , "check" , array($source , self::$tags));
		$source = \mynt::exec("\\lib\\html\\ptn_for" , "check" , array($source , self::$tags));
		return $source;
	}


	public static function getProcs($key , $proc , $val){
		$res = "";
		switch(strtoupper($key)){
			case "CLASS":
				$res = self::getProcs_class($proc , $val);
				break;
			case "FUNCTION":
				$res = self::getProcs_function($proc , $val);
				break;
			case "PROC":
				$res = self::getProcs_proc($proc , $val);
				break;
		}
		return $res;
	}

	public static function getCodes($key , $val){
		$res = "";
		switch(strtoupper($key)){
			case "EVAL":
				$res = self::getCodes_code($val);
				break;
			case "CODE":
				$res = self::getCodes_code($val);
				break;
			case "FILE":
				$res = self::getCodes_file($val);
				break;
		}
		return $res;
	}

  public static function getProcs_class($func , $val){
    $data = explode("/" , $func);
    if(count($data)!==2 || !class_exists($data[0])){return "";}
		$query = ($val=="")?array():explode(",",$val);
		
		for($i=0,$c=count($query); $i<$c; $i++){
			$query[$i] = str_replace('"' , '' , $query[$i]);
			$query[$i] = str_replace("'" , "" , $query[$i]);
		}

    if(!method_exists($data[0],$data[1])){return;}
		$cls = new $data[0];

		return call_user_func_array(array($cls , $data[1]) , $query);
	}
	
	public static function getProcs_proc($func,$val){
    $data = explode("/" , $func);
    if(count($data)!==2 || !class_exists($data[0])){return "";}
    $query = ($val=="")?array():explode("," ,$val);
 
		for($i=0,$c=count($query); $i<$c; $i++){
			$query[$i] = str_replace('"' , '' , $query[$i]);
			$query[$i] = str_replace("'" , "" , $query[$i]);
		}

    if(!method_exists($data[0] , $data[1])){return;}

		return call_user_func_array($data[0]."::".$data[1] , $query);
  }


	

	

	public static function changeQueryArray($vals=""){
		if($vals === ""){
			return array();
		}
		else if(preg_match("/\((.*?)\)/" , $vals , $match)){
			$vals = array($match[1]);
		}

		// res
		if($vals === ""){
			return array();
		}
		else{
			$valArr = str_getcsv($vals);
			return $valArr;
		}
	}

	public static function getProcs_function($func,$val){
    if(!function_exists($func)){return "";}

    $query = ($val=="") ? array() : explode("," , $val);

		for($i=0,$c=count($query); $i<$c; $i++){
			$query[$i] = str_replace('"' , '' , $query[$i]);
			$query[$i] = str_replace("'" , "" , $query[$i]);
		}

		return call_user_func_array($func , $query);
  }

  public static function getData_FOR($val){
		// 1:start-num , 2:end-num , 3:count-up , 4:string-format("--%num%--")
    preg_match("/^(.*?),(.*?),(.*?):(.*?)$/s" , $val , $match);	
    if(count($match)!==5){return $val;}

    $val1 = self::getPattern_Lite($match[1]);
    $val2 = self::getPattern_Lite($match[2]);
    $val3 = self::getPattern_Lite($match[3]);

    $value="";
    for($i=$val1; $i<=$val2; $i=$i+$val3){
      $str = $match[4];
      $str = str_replace("%num%" , $i , $str);
      $value.= $str;
    }
    $value = self::getPattern_Lite($value);
    return $value;
  }

	public static function getCodes_code($val){
		if(!$val){return;}
    return eval($val);
  }

  public static function getCodes_file($path){
    if(!is_file($path)){return;}
    $source = file_get_contents($path);
    $source = self::conv($source);
    return $source;
  }

  public static function getData_IF($val){
		$sp = explode(":",$val);
		if($sp[0]){
			return $sp[1];
		}
		else{
			return $sp[2];
		}
  }
}
