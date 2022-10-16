<?php
namespace lib\blog;

class info{

  // get-blog-info
  public static function getInfo($category="" , $id=""){
    $keys  = array();
    $where = array(
      "id"       => $id,
      "category" => $category
    );
    $sort  = array();
    $res   = \mynt::data_load("" , "lib_blog" , $keys , $where , $sort);
    if($res["status"] === "ok" && count($res["data"])){
      return $res["data"][0];
    }
    else{
      return null;
    }
	}

	public static function getString($key="" , $category="1" , $id="" , $format=""){
    if(!$key || !$id){return;}
    $category = $category ? $category : "1";
    $info = self::getInfo($category , $id);

    $res = isset($info[$key]) ? $info[$key] : "";
    if(!$res){return "";}

    switch($format){
      case "ymdhis" :
        $y = substr($res , 0 , 4);
        $m = substr($res , 4 , 2);
        $d = substr($res , 6 , 2);
        $h = substr($res , 8 , 2);
        $i = substr($res , 10 , 2);
        return $y."-".$m."-".$d."T".$h.":".$i;

      default :
        return $res;
    }
  }

}