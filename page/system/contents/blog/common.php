<?php
namespace page\system\contents\blog;

class common{

	public static $pageLists = array();
	// $schecule @ ["" : 全て表示(default:管理用) , "past" : 現在より過去を表示(ブログ通常表示用) , "future" : 現在より未来を表示]
	public static function getLists($status="default" , $category="" , $schecule=""){
		$category = ($category) ? $category : "1";
		$status = ($status) ? $status : "default";
		$currentDate = date("YmdHis");
    if(!isset(self::$pageLists[$status]) || !isset(self::$pageLists[$status][$category])){
			$lists = array();
			$datas = self::getLists_all($category);
			foreach($datas as $filename => $data){

				// 
				if($schecule && isset($data["date"]) && $data["date"]){
// echo $currentDate ." < ".$data["date"].PHP_EOL;
					if($schecule === "past"){
						if((int)$currentDate < (int)$data["date"]){continue;}
					}
					else if($schecule === "past"){
						if((int)$currentDate > (int)$data["date"]){continue;}
					}
				}

				if($status === "default"){
					$lists[] = $data;
				}
				else if($status === "unregist" && (!isset($data["status"]) || !$data["status"])){
					$lists[] = $data;
				}
				else if(isset($data["status"]) && $status === $data["status"]){
					$lists[] = $data;
				}
			}
			self::$pageLists[$status][$category] = $lists;
		}
    return self::$pageLists[$status][$category];
  }

  public static $pageListsAll = array();
  public static function getLists_all($category=""){
		$category = ($category) ? $category : "";
		if(!isset(self::$pageListsAll[$category])){
			$keys  = array();
			$where = array("category" => $category);
			$sort  = array("date" => "SORT_DESC");
			$datas = \mynt::data_load("" , "lib_blog" , $keys , $where , $sort);
			if($datas["status"] === "ok"){
				self::$pageListsAll[$category] = $datas["data"];
			}
			else{
				self::$pageListsAll[$category] = array();
			}
		}
    return self::$pageListsAll[$category];
  }

  public static function getCount($status="" , $category=""){
		$lists = self::getLists($status , $category);
		return count($lists);
  }
  
	public static function getKey2Value($data , $key){
		$res = "--";
		for($i=0; $i<count($data); $i++){
			if(isset($data[$i]["key"]) && $data[$i]["key"] === $key){
				$res = $data[$i]["value"];
				break;
			}
		}
		return $res;
	}

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
	
	// public static function getDataPath($category="" , $id=""){
	// 	if(!$id){return;}
	// 	$category = $category ? $category : "1";
	// 	$info = self::getInfo($category,$id);
	// 	if(!$info){return;}
	// 	if(!isset($info["file"]) || !$info["file"]){return;}
	// 	$setting = \mynt::exec('\lib\data\database','getSetting');
	// 	return "data/".$setting["database"]."/data_blog/".$category."/".$info["file"].".html";
	// }

  // public static function getSource($category="" , $id=""){
	// 	$path = self::getDataPath($category,$id);
	// 	if(!$path || !is_file($path)){return;}
	// 	$txt = file_get_contents($path);
	// 	return htmlspecialchars($txt);
	// }
  


  

}