<?php
namespace lib\html;

class method{

  // checl-method
	public static function check($source , $tags){
		$ptn = $tags[0].'method\:(.+?)\:\:(.+?)\((.*?)\)'.$tags[1];
		if(preg_match_all("/".$ptn."/is" , $source  , $match)){
			for($i=0, $c=count($match[1]); $i<$c; $i++){
				if($match[0][$i]===""){continue;}
				$res    = self::getMethod($match[1][$i] , $match[2][$i] , $match[3][$i]);
				$source = str_replace($match[0][$i] , $res , $source);
			}
		}
		return $source;
  }

  
  public static function getMethod($cls , $func , $val=""){
		$res = "";
		if(class_exists($cls) && method_exists($cls , $func)){
			// function
			if(method_exists($cls, $func)){
        $querys = \mynt::exec("\\lib\\html\\replace","changeQueryArray" , array($val));
				for($i=0,$c=count($querys); $i<$c; $i++){
					if(!isset($querys[$i])){continue;}
					$querys[$i] = str_replace(array('"',"'") , '' , $querys[$i]);
				}
				$res = call_user_func_array($cls."::".$func , $querys);
			}
		}
		return $res;
	}
}