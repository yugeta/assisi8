<?php
namespace page\system\contents\media;

class view{

  public static $datas = null;
  public static function getDatas(){
    if(self::$datas === null){
      $where = array();
      $sort  = array(
        "id" => "SORT_DESC"
      );
      self::$datas = \mynt::data_load('','lib_media',array(),$where,$sort);
    }
    return self::$datas;
  }

  public static function files($last_file="" , $view_count=10){
    $dir = \mynt::exec('\page\system\contents\media\picture','getImageDir');
    if(!$dir){return;}

    $datas = self::getDatas();
    if(!$datas || $datas["status"] === "error" || !isset($datas["data"])){return;}

    $cnt       = 0;
    $finish    = false;
    $nextCount = 0;
    $pickups   = array();
    foreach($datas["data"] as $data){
      if($finish === true){
        $nextCount++;
        continue;
      }
      $filename  = $data["currentName"] .".". $data["extension"];
      if(!is_file($dir.$filename)){continue;}

      // on the way
      if($last_file){
        if($filename === $last_file){
          $last_file = "";
          continue;
        }
        else{
          continue;
        }
      }
      // $pickups[] = $filename;
      $pickups[] = $data;

      // count-over
      if(count($pickups) >= $view_count){
        $finish = true;
        // break;
      }
    }

    $res_array = array(
      "dir"   => $dir,
      "files" => $pickups,
      "nextCount"  => $nextCount
    );
    return json_encode($res_array , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }


}