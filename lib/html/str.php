<?php
namespace lib\html;

class str{
  public static function check($source , $tags){
		$ptn = $tags[0].'method\:(.+?)\:\:(\$.+?)'.$tags[1];
		if(preg_match_all("/".$ptn."/is" , $source  , $match)){
			for($i=0, $c=count($match[1]); $i<$c; $i++){
				if($match[0][$i]===""){continue;}
        $res = \mynt::exec("\\lib\\html\\method" , "getMethod" , array($match[1][$i] , $match[2][$i] , ""));
				$source = str_replace($match[0][$i] , $res , $source);
			}
		}
		return $source;
	}
}