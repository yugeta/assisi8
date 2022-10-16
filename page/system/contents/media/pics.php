<?php
namespace page\system\contents\media;

class pics{

  public static function view_li($uid="",$group=0,$count=10,$lastId=""){
    if(!$uid){return;}
    $group = ($group) ? $group : 0;

    $path = self::getImagePath($uid,$group);
    if(!$path){return;}


    $lists = self::getLists($uid);
    if(!$lists || !count($lists)){return;}

    $html = "";
    $cnt  = 0;
    for($i=count($lists)-1; $i>=0; $i--){
      if(!is_file($path.$lists[$i])){continue;}
      if($count && $cnt >= $count){break;}
      $pathinfo = pathinfo($lists[$i]);
      $html .= "<li>";
      $html .= "<div class='pic' data-id='".$pathinfo["filename"]."'>";
      $html .= "<div class='num'></div>";
      $html .= "<img src='mynt/service/img/loading.svg' data-src='".$path.$lists[$i]."' data-type='loading'>";
      $html .= "</pic>";
      $html .= "</li>";
      $cnt++;
    }
    return $html;
  }
  

  public static function getLists($uid="",$group="0"){
    if(!$uid){return;}

    $path = self::getImagePath($uid,$group);
    if(!$path){return;}
    
    unset($res);
    exec("ls -v ".$path , $res);
    return $res;
  }

  public static function getImagePath($uid="",$group=""){
    if(!$uid || $group===""){return null;}
    $config = \mynt::exec("\\mynt\\lib\\config","getData");
    $path = $config["dir"].$config["database"]."/images/".$uid."/".$group."/";
    if(is_dir($path)){
      return $path;
    }
    else{
      return null;
    }
  }

  // 撮影済みの写真一覧の取得
  public static function ajax_getPictureLists_past($uid="",$folder="",$lastID="",$picture_count=10){

    if(!$uid){exit();}
    $folder = ($folder) ? $folder : 0;

    // file_path
    $dir = self::getImagePath($uid,$folder);

    // get-all-lists
    $fileLists = self::getLists($uid,$folder);
    $fileLists = array_reverse($fileLists);

    $newLists = array();

    $next = 0;

    // last-id無し（初回）
    if(!$lastID){
      $flg = true;
    }

    // last-id有り（２回目以降）
    else{
      $flg = false;
    }

    for($i=0; $i<count($fileLists); $i++){
      $image_path = $dir.$fileLists[$i];
      if(!is_file($image_path)){continue;}

      $fileinfo = pathinfo($fileLists[$i]);
      $id = $fileinfo["filename"];
      if($flg === false){
        if($id == $lastID){
          $flg = true;
        }
        continue;
      }

      $wheres = array("uid_code"=>$uid,"id"=>$id);
      $res = \mynt::exec("\\mynt\\lib\\data" , "load" , array("" , "image_info" , array() , $wheres));
      if($res["status"] === "error"){continue;}

      // $info = ($res["status"] !== "error") ? json_decode($res["data"][0]["data"],true)  : "";
      $info = json_decode($res["data"][0]["data"],true);

      $orientation = (isset($info["exif"]["Orientation"]) && $info["exif"]["Orientation"]) ? (int)$info["exif"]["Orientation"] : 1;
      $rotate      = (isset($info["info"]["rotate"]) && $info["info"]["rotate"]) ? (int)$info["info"]["rotate"] : 0;
      $extension   = (isset($info["info"]["ext"])) ? $info["info"]["ext"] : "";

      $image_path = array(
        "u" => (int)$uid,
        "g" => $folder,
        "i" => (int)$id,
        "o" => $orientation,
        "r" => $rotate,
        "e" => $extension,
        "s" => (isset($_REQUEST["size"]) && $_REQUEST["size"]) ? (int)$_REQUEST["size"] : 200
      );

      $data = array(
        "id"   => $id,
        "bid"  => $res["data"][0]["bid"],
        "ext"  => $fileinfo["extension"],
        "src"  => $image_path,
        "info" => $info,
        "rotate" => 90,
        "image_path_base64" => base64_encode(json_encode($image_path))
      );
      
      array_push($newLists , $data);
      // 上限数を超えたら終了
      if($picture_count > 0
      && $picture_count !== null
      && count($newLists) >= $picture_count){
        if($i < count($fileLists)-1){
          $next = 1;
        }
        break;
      }
    }

    $newData = array(
      "lists" => $newLists,
      "next"  => $next,
      "total" => count($fileLists)
    );
    
    echo json_encode($newData , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
  }

  // // 1枚の画像ファイルパスからimage.phpのサムネイルクエリを作成
  // public static function getPath2Query($path){
  //   if(!$path || !is_file($path)){return;}

  //   preg_match("/data\/smful\/images\/(.+?)\/(.+?)\/(.+?)\.(.+?)$/" , $path , $match);

  //   $wheres = array("uid_code"=>$match[1],"id"=>$match[3]);
  //   $res = \mynt::exec("\\mynt\\lib\\data" , "load" , array("" , "image_info" , array() , $wheres));
  //   $info = ($res["status"] !== "error") ? json_decode($res["data"][0]["data"],true)  : "";
    
  //   $orientation = (isset($info["exif"]["Orientation"]) && $info["exif"]["Orientation"]) ? (int)$info["exif"]["Orientation"] : 1;
  //   $rotate      = (isset($info["info"]["rotate"]) && $info["info"]["rotate"]) ? (int)$info["info"]["rotate"] : 0;
  //   $extension   = (isset($info["info"]["ext"])) ? $info["info"]["ext"] : "";

  //   $image_path = array(
  //     "u" => (int)$uid,
  //     "g" => $folder,
  //     "i" => (int)$id,
  //     "o" => $orientation,
  //     "r" => $rotate,
  //     "e" => $extension,
  //     "s" => (isset($_REQUEST["size"]) && $_REQUEST["size"]) ? (int)$_REQUEST["size"] : 200
  //   );

  //   $data = array(
  //     "id"   => $id,
  //     "ext"  => $fileinfo["extension"],
  //     "src"  => $image_path,
  //     "info" => $info,
  //     "rotate" => 90,
  //     "image_path_base64" => base64_encode(json_encode($image_path))
  //   );
  // }

  // 新規に撮影された写真データ（一覧）の取得
  public static function ajax_getPictureLists_new($uid="",$folder="",$firstID=""){
    if(!$uid){exit();}
    $folder = ($folder) ? $folder : 0;

    // file_path
    $dir = self::getImagePath($uid,$folder);

    // get-all-lists
    $fileLists = self::getLists($uid,$folder);
    // $fileLists = array_reverse($fileLists);

    $newLists = array();

    $flg = false;

    for($i=0; $i<count($fileLists); $i++){
      $image_path = $dir.$fileLists[$i];
      if(!is_file($image_path)){continue;}

      $fileinfo = pathinfo($fileLists[$i]);
      $id = $fileinfo["filename"];
      if($firstID && $flg === false){
        if($id == $firstID){
          $flg = true;
        }
        continue;
      }

      $wheres = array("uid_code"=>$uid,"id"=>$id);
      $res = \mynt::exec("\\mynt\\lib\\data" , "load" , array("" , "image_info" , array() , $wheres));
      $info = ($res["status"] !== "error") ? json_decode($res["data"][0]["data"],true)  : "";
      
      $orientation = (isset($info["exif"]["Orientation"]) && $info["exif"]["Orientation"]) ? (int)$info["exif"]["Orientation"] : 1;
      $rotate      = (isset($info["info"]["rotate"]) && $info["info"]["rotate"]) ? (int)$info["info"]["rotate"] : 0;

      $image_path = array(
        "u" => (int)$uid,
        "g" => $folder,
        "i" => (int)$id,
        "o" => $orientation,
        "r" => $rotate,
        "e" => "jpg",
        "s" => (isset($_REQUEST["size"]) && $_REQUEST["size"]) ? (int)$_REQUEST["size"] : 200
      );

      $data = array(
        "id"   => $id,
        "bid"  => $res["data"][0]["bid"],
        "ext"  => $fileinfo["extension"],
        "src"  => $image_path,
        "info" => $info,
        "rotate" => 90,
        "image_path_base64" => base64_encode(json_encode($image_path))
      );
      array_push($newLists , $data);
    }

    $newData = array(
      "lists" => $newLists,
      "total" => count($fileLists)
    );

    echo json_encode($newData , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
  }


  // 写真撮影した直後の自分の写真をリストに登録
  public static function ajax_getPictureLists_take($bid="",$lastID="",$picture_count=10){
    if(!$bid){exit();}

    $wheres = array("bid"=>$bid);
    $res = \mynt::exec("\\mynt\\lib\\data" , "load" , array("" , "take_photo" , array() , $wheres));

    if($res["status"] === "error"){
      return "[]";
    }

    // last-id無し（初回）
    if(!$lastID){
      $flg = true;
    }

    // last-id有り（２回目以降）
    else{
      $flg = false;
    }
    

    $next = 0;
    $newLists = array();
    for($i=0; $i<count($res["data"]); $i++){
      $uid = $res["data"][$i]["take_uid"];
      $gid = $res["data"][$i]["image_group_code"];
      $id  = $res["data"][$i]["image_id"];
      $ext = $res["data"][$i]["ext"];
      if($gid === null || $gid === false || $gid === ""){continue;}
      $dir = self::getImagePath($uid,$gid);
      $image_path = $dir .$id.".".$ext;
      if($flg === false){
        if($id == $lastID){
          $flg = true;
        }
        continue;
      }

      $wheres = array("uid_code"=>$uid,"id"=>$id);
      $res2 = \mynt::exec("\\mynt\\lib\\data" , "load" , array("" , "image_info" , array() , $wheres));
      $info = ($res["status"] !== "error") ? json_decode($res2["data"][0]["data"],true)  : "";
      $orientation = (isset($info["exif"]["Orientation"]) && $info["exif"]["Orientation"]) ? $info["exif"]["Orientation"] : 1;

      $base64 = array(
        "u" => $uid,
        "g" => $gid,
        "i" => $id,
        "o" => $orientation,
        "s" => (isset($_REQUEST["size"]) && $_REQUEST["size"]) ? $_REQUEST["size"] : 200
      );

      array_push($newLists , array(
        "id"   => $id,
        "bid"  => $res2["data"][0]["bid"],
        "ext"  => $ext,
        "src"  => $image_path,
        "info" => $info,
        "rotate" => 90,
        "image_path_base64" => base64_encode(json_encode($base64))
      ));

      if(count($newLists) >= $picture_count){
        if($i < count($res["data"])-1){
          $next = 1;
        }
        break;
      }
    }

    $newData = array(
      "lists" => $newLists,
      "next"  => $next,
      "total" => count($res["data"])
    );

    return json_encode($newData , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }


  // public static function redirect_folder_id($folder_id=""){
  //   if($folder_id !== ""){return;}
  //   $folder_id = \mynt::exec("\\mynt\\service\\folder","get_currentImageGroupCode",array($_SESSION["id"]));
  //   $folder_id = ($folder_id!=="") ? $folder_id : 0;
  //   $url = \mynt::exec("\\mynt\\lib\\url","getUri") . "&folder=" . $folder_id;
  //   \mynt::exec("\\mynt\\lib\\url","setUrl",array($url));
  // }

  // public static function getTargetUid(){
  //   $res = \mynt::exec("\\mynt\\service\\qr" , "getQueryData" , array());
  //   if(!$res || $res["status"] === "error"){
  //     return "";
  //   }
  //   else{
  //     return $res["data"]["uid_code"];
  //   }
  //   // $query = $_SERVER['QUERY_STRING'];
  //   // $sp = explode("&",$query);
  //   // return $sp[0];
  // }
  // public static function getTargetFolder(){
    
  // }

  public static function removeImage($uid="" , $wid="" , $pid="" , $bid=""){
    if(!$uid || !$wid || !$pid){return;}
    // return "removeImage : ".$uid."/".$wid."/".$pid;

    $update = date("YmdHis");

    $saveData = array(
      "uid_code" => $uid,
      "bid"      => $bid,
      "image_group_code" => $wid,
      "id"       => $pid,
      "entry"    => $update
    );
    $wheres = array(
      "uid_code" => $uid,
      "bid"      => $bid,
      "image_group_code" => $wid,
      "id"       => $pid
    );

    $res = \mynt::data_del(array("","image_info",$saveData,$wheres));
    return json_encode($res , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }
  


}