<?php
namespace lib\media;

class common{
  
  // dir
  public static $default_pic_dir = "media";
  public static $default_pic_path = null;
	public static function getDir(){
		if(self::$default_pic_path === null){
			$setting = \mynt::exec('\lib\data\database','getSetting');
			self::$default_pic_path = "data/".$setting["database"]."/".self::$default_pic_dir."/";
		}
		return self::$default_pic_path;
  }

  // db(info)-load
  public static function getInfo($filename=""){
    if(!$filename){return;}
    $where = array(
      "currentName" => $filename
    );
    $res = \mynt::data_load($GLOBALS["config"]["data"] , 'lib_media' , array() , $where);
    if($res["status"] === "ok"){
      return $res["data"][0];
    }
    else{
      return null;
    }
  }

  // db(info)-del
  public static function delInfo($filename=""){
    if(!$filename){return;}
    $where = array(
      "currentName" => $filename
    );
    $res = \mynt::data_del($GLOBALS["config"]["data"] , 'lib_media' , array() , $where);
    return json_encode($res , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    // if($res["status"] === "ok"){
    //   return $res["data"][0];
    // }
    // else{
    //   return null;
    // }
  }

  

}