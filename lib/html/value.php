<?php
namespace lib\html;

class value{
  // 
	public static function get($key , $val){
		$res = "";
		switch(strtoupper($key)){
			case "POST":
				$res = self::getArrayValue($_POST , $val);
				break;
			case "GET":
				$res = self::getArrayValue($_GET , $val);
				break;
			case "REQUEST":
				$res = self::getArrayValue($_REQUEST , $val);
				break;
			case "GLOBALS":
				$res = self::getArrayValue($GLOBALS , $val);
				break;
			case "DEFINE":
				$res = constant($val);
				break;
			case "SESSION":
				$res = (isset($_SESSION)) ? self::getArrayValue($_SESSION , $val) : "";
				break;
			case "SERVER":
				$res = self::getArrayValue($_SERVER,$val);
				break;
			case "CONFIG":
				if(isset($GLOBALS["config"])){
					$res = self::getArrayValue($GLOBALS["config"] , $val);
				}
				break;
		}
		return $res;
	}
	

	public static function getArrayValue($datas , $key=""){
		$key = (gettype($key) === "array" && count($key) === 1) ? $key[0] : $key;

		if(gettype($key) === "string"){
			return self::getArrayValue_string($datas , $key);
		}
		else if(gettype($key) === "array"){
			return self::getArrayValue_array($datas , $key);
		}
		else{
			return "";
		}
	}
  
  public static function getArrayValue_string($datas , $key=""){
		if(isset($datas[$key])){
			return (string)$datas[$key];
		}
		else{
			return "";
		}
	}

	public static function getArrayValue_array($datas , $keys=array()){
		if(!$keys){return "";}
		$first_key = array_shift($keys);
		if(!isset($datas[$first_key])){
			return "";
		}
// print_r($keys);
		// return self::getArrayValue($datas[$first_key] , implode("/" , $keys));
		return self::getArrayValue($datas[$first_key] , $keys);
	}


}