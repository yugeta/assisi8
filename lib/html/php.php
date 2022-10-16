<?php
namespace lib\html;

class php{
  // check-php
	public static function check($source , $tags){
		$ptn = $tags[0].'php\:(.+?)\:\:(.+?)\((.*?)\)'.$tags[1];
		if(preg_match_all("/".$ptn."/is" , $source  , $match)){//print_r($match[1]);
			for($i=0, $c=count($match[1]); $i<$c; $i++){
				if($match[0][$i]===""){continue;}
				$res    = self::get($match[1][$i] , $match[2][$i] , $match[3][$i]);
				$source = str_replace($match[0][$i] , $res , $source);
			}
		}
		return $source;
  }
  

  public static function get($cls , $func , $val=""){
		$res = "";
// echo $cls."\n";
		if(self::checkClass($cls)){
			$cls = \mynt::class_name_replace($cls);
// echo $cls."\n";
			// function
			if(method_exists($cls , $func)){
        $querys = \mynt::exec("\\lib\\html\\replace" , "changeQueryArray" , array($val));
				for($i=0,$c=count($querys); $i<$c; $i++){
					if(!isset($querys[$i])){continue;}
					$querys[$i] = str_replace(array('"' , "'") , '' , $querys[$i]);
				}
// echo $cls ."::". $func."\n";
				$res = call_user_func_array($cls ."::". $func , $querys);
			}
		}
		return $res;
	}
	
	// public static function value_disassembly($val=""){
	// 	if($val===""){return;}
	// 	if(strpos($val,",")){return;}

	// }
  
  public static function checkClass($class){
		if(!$class){
			return false;
		}
		if(class_exists(\mynt::class_name_replace($class))){
			return true;
		}

		// 存在しないclassは読み込み処理を実行
		return \lib\main\init::checkModuleLoad($class);
	}
}