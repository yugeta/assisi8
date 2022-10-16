<?php
namespace lib\html;

class file{
  // pattern (file)
	public static function check($source , $tags){
		$keys = array("eval","file");
		$ptn   = $tags[0].'('.implode('|',$keys).')\:(.+?)'.$tags[1];
		preg_match_all("/".$ptn."/is" , $source  , $match);
		if(count($match[1])){
			for($i=0, $c=count($match[1]); $i<$c; $i++){
				if($match[0][$i]===""){continue;}
        $res    = \mynt::exec('\lib\html\replace' , "getCodes" , array($match[1][$i] , $match[2][$i]));
				$source = str_replace($match[0][$i] , $res , $source);
			}
		}
		return $source;
	}

	public static function getFile($path=""){
		if(!$path || !is_file($path)){return;}
		$temp = file_get_contents($path);
		return \MYNT::exec('\lib\html\replace','conv',array($temp));
	}
}