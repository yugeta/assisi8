<?php
namespace page\system\contents\blog;

class group{

  public static $data = array();
  public static function getData(){
		if(isset(self::$data["group"])){return self::$data["group"];}
		// if(!isset($GLOBALS["config"]["group"]) || !count($GLOBALS["config"]["group"])){return null;}

		self::$data["group"] = array();

		for($i=0,$c=count($GLOBALS["config"]["group"]); $i<$c; $i++){
			self::$data["group"][$GLOBALS["config"]["group"][$i]["id"]] = $GLOBALS["config"]["group"][$i];
		}
		return self::$data["group"];
  }
  

}