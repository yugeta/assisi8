<?php
namespace page\system\contents\media;

class upload{
  public static function up_image(){

    // 元ファイル名を取得
    $filename = \mynt::exec('\plugin\fileupload\src\php\image','getFilename');
    if(!$filename){return;}

    $savename = $_REQUEST["id"]."_".$_REQUEST["num"];

    // save
    $res_info = self::save_info_image($filename , $savename);

    $res_img  = self::save_file_image($res_info);

    $datas = array(
      "dir"   => \mynt::exec('\page\system\contents\media\picture','getImageDir'),
      "file"  => $res_info["data"]["currentName"] .".". $res_info["data"]["extension"],
      "files" => array($res_info["data"]["currentName"] .".". $res_info["data"]["extension"])
    );
// error_log($datas["dir"]);
    // return $res_img;
    return json_encode($datas , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  // save-image-info
  public static function save_info_image($filename="" , $savename=""){
    if(!$filename || !$savename){return;}

    $update = date("YmdHis");

    $datas = array(
      "type"        => self::getType($filename),
      "currentName" => $savename,
      "fileName"    => $filename,
      "extension"   => strtolower(\mynt::exec('\plugin\fileupload\src\php\image','getExtension',array($filename))),
      "size"        => \mynt::exec('\plugin\fileupload\src\php\image','getFilesize'),
      "memo"        => "",
      "entry"       => $update
    );

    $res = \mynt::data_save("","lib_media",$datas);
    return $res;
  }

  // save-image-file
  public static function save_file_image($res_info=array()){
    if(!$res_info){return;}
    if($res_info["status"] !== "ok"){return;}

    // 保存領域の確認
    $dir = \mynt::exec('\page\system\contents\media\picture','getImageDir');
    if(!is_dir($dir)){
      mkdir($dir , 0777 , true);
    }

    // 保存パス
    $savePath = $dir.$res_info["data"]["currentName"] .".". $res_info["data"]["extension"];
    // return $savePath;
    return \mynt::exec('\plugin\fileupload\src\php\image','save',array($savePath));
  }


  // ファイル名（拡張子）を元に、ファイルタイプを取得する。
  public static function getType($filename=""){
    if(!$filename){return;}
    $extension = \mynt::exec('\plugin\fileupload\src\php\image','getExtension',array($filename));

    switch(strtolower($extension)){
      // image
      case "jpg":
      case "jpeg":
      case "png":
      case "gif":
      case "svg":
        return "image";
      // sound
      case "mp3":
      case "m4a":
        return "sound";
      // video
      case "mp4":
      case "m4v":
        return "video";
      // etc
      default:
        return "file";
    }
  }

  public static function up_file(){
//echo "file-upoload---";exit();
// return json_encode($_FILES , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    // 元ファイル名を取得
    // $filename = \mynt::exec('\plugin\fileupload\src\php\image','getFilename');
    // $filename = ;
    // if(!$filename){return;}
// return $filename;
    // $savename = $_REQUEST["id"];
    $datas = array();
    
    if(!$_FILES || $_FILES["file"]["error"] == 1){
      $datas = array(
        "status" => "error",
        "exists_tmp" => $_FILES
      );
      // $datas["exists_tmp"]   = json_encode($_FILES,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    else{
      // save
      $dir = \mynt::exec('\page\system\contents\media\picture','getImageDir');
      $ext = strtolower(\mynt::exec('\plugin\fileupload\src\php\image','getExtension',array($_FILES["file"]["name"])));
  // $basepath = $dir . $_POST["id"] .".". $ext;
  // $savepath = $dir . $_FILES["file"]["name"];
  // return $basepath.PHP_EOL. $savepath;
      move_uploaded_file($_FILES["file"]["tmp_name"] , $dir . $_POST["id"] .".". $ext);
      

      // $res_img  = self::save_file_image($res_info);

      $datas = self::save_info_image($_FILES["file"]["name"] , $_POST["id"]);

     
      // $datas["exists_file"]  = $dir . $_POST["id"] .".". $ext;
      // $datas["exists_check"] = is_file($dir . $_POST["id"] .".". $ext);
    }
    // $datas = array(
    //   "dir"   => $dir,
    //   "file"  => $_POST["id"] .".". $ext,
    //   // "files" => array($res_info["data"]["currentName"] .".". $res_info["data"]["extension"])
    // );
    return json_encode($datas , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }


}