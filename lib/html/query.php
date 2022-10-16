<?php
namespace lib\html;

class query{

  public static $keys = array(
    "post",
    "get",
    "request",
    "globals",
    "define",
    "session",
    "server",
    "config"
  );

  // pattern (querys)
	public static function check($source , $tags){
		$ptn  = $tags[0].'('. implode('|' , self::$keys) .')\:(.+?)'.$tags[1];
		preg_match_all("/".$ptn."/is" , $source  , $match);
		if(count($match[1])){
			for($i=0, $c=count($match[1]); $i<$c; $i++){
				if($match[0][$i]===""){continue;}
				$key = $match[1][$i];
// print_r($match[2][$i].PHP_EOL);
				// $val = $match[2][$i];
				$val = self::replace_multiquery($match[2][$i]);
// echo "-".PHP_EOL;
// print_r($val);

				$res = \mynt::exec("\\lib\\html\\value" , "get" , array($key , $val));
        
				if(is_string($res)){
					$source = str_replace($match[0][$i] , $res , $source);
				}
				else{
					$source = str_replace($match[0][$i] , "" , $source);
				}
			}
		}
		return $source;
	}
	
	// postやgetの"query[aaa][bbb]"などの階層対応
	public static function replace_multiquery($query_code=""){
		if(preg_match("/^(.+?)(\[.+?\])$/" , $query_code , $match)){
			$arr = array($match[1]);
			preg_match_all("/\[(.+?)\]/" , $match[2] , $matches);
			for($i=0; $i<count($matches[1]); $i++){
				array_push($arr , $matches[1][$i]);
			}
			return $arr;
		}
		else{
			return $query_code;
		}
		
	}
}