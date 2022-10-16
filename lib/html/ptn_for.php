<?php
namespace lib\html;

class ptn_for{
  // pattern (for)
	public static function check($source , $tags){
		$ptn = $tags[0].'for\((.*?)\.\.(.*?)\)'.$tags[1].'(.+?)'.$tags[0].'\/for'.$tags[1];
		preg_match_all("/".$ptn."/is" , $source  , $match);
		if(count($match[1])){
			for($i=0, $c=count($match[1]); $i<$c; $i++){
				if($match[0][$i]===""){continue;}
				$str = $match[3][$i];
				$str = str_replace('"','\"',$str);
				$str = str_replace("\n",'',$str);
				$match[2][$i] = ($match[2][$i] === "")?$match[1][$i]:$match[2][$i];
				$evalStr = '$s=""; for($j='.$match[1][$i].'; $j<='.$match[2][$i].'; $j++){$s.= str_replace("##",$j,"'.$str.'");} return $s;';
				$res = eval($evalStr);
				$source = str_replace($match[0][$i] , $res , $source);
			}
		}
		return $source;
	}
}