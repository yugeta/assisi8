<?php
namespace lib\image;

class view{

  public static function file($imagePath){
    if(!$imagePath || !is_file($imagePath)){return;}

    $pathinfo = pathinfo($imagePath);
    if(!isset($pathinfo["extension"])){return;}

    $contentType = \mynt::exec('\lib\image\file_control','ext2contentType',array($pathinfo["extension"]));

    header('Content-Type: '.$contentType);
    readfile($imagePath);
  }

  // // テンプレートに無い場合、生成
  // public static function viewThumbnail($base64 , $data){

  //   $path = self::getFilepath($base64 , $data);
  //   if(is_file($path)){
  //     \mynt::exec("\\mynt\\lib\\image","viewImage",array($path));
  //   }
  //   else{
  //     return;
  //   }
  // }

  // public static function viewThumbnail_direct($base64 , $data){

  //   $config = \mynt::exec("\\mynt\\lib\\config","getData");
  //   $path = $config["dir"].$config["database"]."/"."images/".$data["u"] ."/". $data["g"] ."/". $data["i"];

  //   if(isset($data["s"]) && $data["s"]){
  //     $size = array($data["s"],$data["s"]);
  //   }
  //   else if(isset($data["w"]) && $data["w"] && isset($data["h"]) && $data["h"]){
  //     $size = array($data["w"],$data["h"]);
  //   }
  //   else{
  //     $size = array(200,200);
  //   }

  //   $orientation = (isset($data["o"])) ? $data["o"] : "1";
  //   $rotate      = (isset($data["r"]) && $data["r"]) ? (int)$data["r"] : 0;

  //   unset($res);
  //   exec("ls ".$path."*" , $res);
  //   if(!count($res)){exit();}
  //   $img_path = $res[0];
    
  //   \mynt::exec("\\mynt\\lib\\image","viewThumbnail",array($img_path , $size , "contain" , $orientation , $rotate));
  // }


  // // mode @ [contain:余白ありで全体表示  cover:トリミングして最大限表示]
  // public static function viewThumbnail($imagePath="" , $viewSize=array(128,128) , $mode="contain" , $orientation=0 , $rotate=0){
  //   $img = self::loadImage($imagePath);
  //   if(!$img){return;}

  //   $currentSize = self::getImageScale($img);

  //   // 軸固定
  //   if($mode === "fixed"){
  //     $changeSize  = self::getAutoScaleChangeSize_fixed($currentSize , $viewSize);
  //     $newImage = imagescale($img, $changeSize[0] , $changeSize[1]);
  //   }
  //   // 全画面表示
  //   else if($mode === "contain"){
  //     $changeSize  = self::getAutoScaleChangeSize_contain($currentSize , $viewSize);
  //     $newImage = imagescale($img, $changeSize[0] , $changeSize[1]);
  //   }
  //   // 固定サイズ
  //   else if($mode === "cover"){
  //     $changeSize = self::getAutoScaleChangeSize_cover($currentSize , $viewSize);
  //     $diffSize   = array(
  //       ($changeSize[0] - $viewSize[0]) /2 * -1,
  //       ($changeSize[1] - $viewSize[1]) /2 * -1
  //     );
  //     $img2 = imagescale($img, $changeSize[0] , $changeSize[1]);
  //     $newImage = imagecreatetruecolor($viewSize[0], $viewSize[1]);
  //     imagecopyresized($newImage, $img2, $diffSize[0], $diffSize[1] , 0, 0, $changeSize[0], $changeSize[1], $changeSize[0], $changeSize[1]);
  //   }
  //   // 通常処理
  //   else{
  //     $newImage = imagescale($img, $viewSize[0] , $viewSize[1]);
  //   }

  //   // orientation
  //   $orientation = self::rotate2orientation($orientation , $rotate);
  //   if($orientation && $orientation != 0 && $orientation != 1){
  //     $newImage = self::orientation($newImage , $orientation);
  //   }
    
  //   $ext = self::getExtension($imagePath);
  //   header('Content-Type: '. self::ext2contentType($ext));
  //   imagejpeg($newImage);
  //   imagedestroy($newImage);
  // }


  // public static function viewImage($imagePath=""){
  //   $img = self::loadImage($imagePath);
  //   if(!$img){return;}
    
  //   $ext = self::getExtension($imagePath);
  //   header('Content-Type: '. self::ext2contentType($ext));
  //   imagejpeg($img);
  //   imagedestroy($img);
  // }
}