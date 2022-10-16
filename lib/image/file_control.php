<?php
namespace lib\image;

class file_control{

  // ファイル名（パス）から拡張子を取得
  public static function getExtension($imgPath=""){
    if(!$imgPath){return null;}

    $info = pathinfo($imgPath);
    return self::arrangeExtension($info["extension"]);
  }

  public static function getFilename($imgPath=""){
    if(!$imgPath){return null;}

    $info = pathinfo($imgPath);
    return $info["basename"];
  }

  // 拡張子を統一規格に整える()
  public static function arrangeExtension($ext=""){
    switch(strtolower($ext)){
      case "jpg":
      case "jpeg":
        return "jpg";
      case "gif":
        return "gif";
      case "png":
        return "png";
      case "svg":
        return "svg";
      default:
        return null;
    }
  }

  // extension->content-type
  public static function ext2contentType($ext){
    switch(strtolower($ext)){
      case "jpg":
      case "jpeg":
        return "image/jpeg";
      case "gif":
        return "image/gif";
      case "png":
        return "image/png";
      case "svg":
        return "image/svg+xml";
      default:
        return null;
    }
  }
  // extension->content-type
  public static function contentType2ext($contentType){
    switch($contentType){
      case "image/jpg":
      case "image/jpeg":
        return "jpg";
      case "image/gif":
        return "gif";
      case "image/png":
        return "png";
      case "image/svg+xml":
        return "svg";
      default:
        return null;
    }
  }

  // 拡張子別画像データの読み込み処理
  public static function loadImage($imgPath=""){
    if(!$imgPath){return false;}

    $ext = self::getExtension($imgPath);
    if(!$ext){return false;}

    switch($ext){
      case "jpg":
        $image = \ImageCreateFromJpeg($imgPath);
        break;
      case "gif":
        $image = \ImageCreateFromGif($imgPath);
        break;
      case "png":
        $image = \ImageCreateFromPng($imgPath);
        break;
      case "svg":
      default:
        return false;
        break;
    }

    return $image;
  }
  public static function loadImage_type($imgPath="" , $type){
    if(!$imgPath){return false;}

    switch($type){
      case "jpeg":
      case "jpg":
        $image = \ImageCreateFromJpeg($imgPath);
        break;
      case "gif":
        $image = \ImageCreateFromGif($imgPath);
        break;
      case "png":
        $image = \ImageCreateFromPng($imgPath);
        break;
      case "svg":
      default:
        return false;
        break;
    }

    return $image;
  }


  // 画像のサムネイル作成
  // mode @ [contain , cover]
  public static function createThumbnail($currentImgPath="" , $saveImgPath="" , $convertSize=array(128,128) , $mode=""){

    if(!$currentImgPath || !is_file($currentImgPath) || !$saveImgPath){return;}
    // if(is_file($saveImgPath)){return;}

    $pathinfo = pathinfo($currentImgPath);
    if(!is_dir($pathinfo["dirname"])){
      mkdir($pathinfo["dirname"] , 0777 , true);
    }

    $image = self::loadImage($currentImgPath);
    if(!$image){return 1;}

    $orientation = 0;
    
    echo self::makeImage($saveImgPath , $image , $convertSize , $orientation);
    exit();
  }

  


  // -----

  public static function makeImage(
    $savePath = "",
    $image ="",
    $newSize=array(),
    $orientation=0){

		if(is_file($savePath)){
			unlink($savePath);
    }

    $ext = self::getExtension($savePath);
    
		// $h = $height / ($width / $w);
    $newImage = imagescale($image, $newSize[0] , $newSize[1]);
    
    // orientation
		switch($orientation){
			case 0: // normal
			case 1: // normal
        break;
        
			case 2: // flip width
				imageflip($newImage, IMG_FLIP_HORIZONTAL);
        break;
        
			case 3: // rotate 180deg
				$color    = imagecolorallocate($newImage, 0, 0, 0);
				$newImage = imagerotate($newImage, -180 , $color);
        break;
        
			case 4: // flip height
				imageflip($newImage, IMG_FLIP_VERTICAL);
        break;
        
			case 5: // flip width + rotate 90deg
				imageflip($newImage, IMG_FLIP_HORIZONTAL);
				$color    = imagecolorallocate($newImage, 0, 0, 0);
				$newImage = imagerotate($newImage, -90 , $color);
        break;
        
			case 6: // rotate 90deg
				$color    = imagecolorallocate($newImage, 0, 0, 0);
				$newImage = imagerotate($newImage, -90 , $color);
        break;
        
			case 7: // flip height + rotate 90deg
				imageflip($newImage, IMG_FLIP_VERTICAL);
				$color    = imagecolorallocate($newImage, 0, 0, 0);
				$newImage = imagerotate($newImage, -90 , $color);
        break;
        
			case 8: // rotate 270deg
				$color    = imagecolorallocate($newImage, 0, 0, 0);
				$newImage = imagerotate($newImage, -270 , $color);
				break;
    }
    
    // make image (need : GD-Library)
		switch($ext){

      case "jpg":
      case "jpeg":
				imagejpeg($newImage , $savePath);
        break;
        
			case "png":
				imagealphablending($newImage, false);
				imagesavealpha($newImage , true);// 透過処理
				imagepng($newImage , $savePath);
        break;
        
			case "gif":
				imagegif($newImage , $savePath);
        break;
        
			default:
				return "error";
    }

    // cache削除
    imagedestroy($newImage);
    
    return "ok";
	}
  

  // public static function getThumnailSize($img=null , $convertSize=array()){
  //   if(!$img || !$convertSize){return;}
  //   // 画像の実サイズ取得
  //   $x = imagesx($img);
  //   $y = imagesy($img);
  // }
  public static function getScale($img=null){
    if(!$img){return null;}
    return array(
      "width"  => imagesx($img),
      "height" => imagesy($img)
    );
  }

}