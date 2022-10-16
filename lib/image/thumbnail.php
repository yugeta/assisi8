<?php
namespace lib\image;

class thumbnail{

  public static function view($path=""){
    if(!$path || !is_file($path)){return;}
    $pathinfo = pathinfo($path);

    switch($pathinfo["extension"]){
      case "svg":
        \mynt::exec('\lib\image\view' , 'file' , array($path));
        break;

      case "mp3":
        \mynt::exec('\lib\image\view' , 'file' , array("lib/image/img/audio.svg"));
        break;
      case "mp4":
        \mynt::exec('\lib\image\view' , 'file' , array("lib/image/img/video.svg"));
        break;


      default:
        $w = isset($_GET["w"]) && $_GET["w"] ? $_GET["w"] : 0;
        $h = isset($_GET["h"]) && $_GET["h"] ? $_GET["h"] : 0;

        // 元サイズが指定サイズよりも小さい場合は、そのまま表示する
        $img = null;
        switch($pathinfo["extension"]){
          case "jpg":
          case "jpeg":
            $img = ImageCreateFromJpeg($path);
            break;
          case "png":
            $img = ImageCreateFromPng($path);
            break;
          case "gif":
            $img = ImageCreateFromPng($path);
            break;
        }
        if($img
        && ($w === 0 || $w >= imagesx($img))
        && ($h === 0 || $h >= imagesy($img))){
          \mynt::exec('\lib\image\view' , 'file' , array($path));
          break;
        }

        // thumbnail階層の取得
        $thumbnail_dir = \mynt::exec('\lib\image\thumbnail','getDir');
        if(!is_dir($thumbnail_dir)){
          mkdir($thumbnail_dir , 0777 , true);
        }

        $thumbnail_file = \mynt::exec('\lib\image\thumbnail','encode_filename',array($pathinfo["dirname"]."/",$pathinfo["filename"],$pathinfo["extension"],$w,$h));

        $thumbnail_path = $thumbnail_dir.$thumbnail_file;

        // サムネイルがない場合はsaveする
        if(!is_file($thumbnail_path)){
          \mynt::exec('\lib\image\thumbnail','saveThumbnail',array($thumbnail_path , $_GET["file"] , $w , $h));
        }
        
        \mynt::exec('\lib\image\view' , 'file' , array($thumbnail_path));
        break;
    }
  }


  public static $dirname = "media_template";
  public static function getDir(){
    $setting = \mynt::exec('\lib\data\database','getSetting');
    if(!isset($setting["database"])){return;}
    return "data/".$setting["database"]."/".self::$dirname."/";
  }

  public static function encode_filename($dir,$name,$ext,$w,$h){
    return base64_encode($dir . $name) .".". $w .".". $h .".". $ext;
  }

  public static function decode_filename($filename){
    $sp = explode(".",$filename);
    $path = base64_decode($sp[0]);
    $pathinfo = pathinfo($path);
    return array(
      "dir"  => $pathinfo["dirname"],
      "file" => $pathinfo["filename"],
      "w"    => $sp[1],
      "h"    => $sp[2],
      "ext"  => $sp[3]
    );
  }


  // サムネイルデータの取得
  public static function saveThumbnail($thumbnailPath="" , $imagePath="" , $w=0 , $h=0 , $orientation=0 , $rotate=0){
    if(!$thumbnailPath || !$imagePath){return;}
// die($thumbnailPath ." : ". $imagePath);
// die("e");
    // サムネイルファイルが作られている場合は処理しない
    if(is_file($thumbnailPath)){return;}
    // 変換元画像が存在しない場合は処理しない
    if(!is_file($imagePath)){return;}

    // 変換サイズの取得（比率自動調整）※サイズに0が入っているとautoとみなす
    $changeSize  = self::getAutoScaleChangeSize($imagePath , $w , $h);
    if(!$changeSize){return;}

    $img = \mynt::exec('\lib\image\file_control','loadImage',array($imagePath));
    $newImage = imagescale($img, $changeSize["width"] , $changeSize["height"]);


    // orientation
    $orientation = \mynt::exec('\lib\image\data','rotate2orientation',array($orientation , $rotate));
    if($orientation && $orientation != 0 && $orientation != 1){
      $newImage = \mynt::exec('\lib\image\data','orientation',array($newImage , $orientation));
    }

    $thumbPathinfo = pathinfo($thumbnailPath);
    if(!is_dir($thumbPathinfo["dirname"])){
      mkdir($dir , 0777 , true);
    }

    switch($thumbPathinfo["extension"]){
      case "jpg":
      case "jpeg":
        imagejpeg($newImage , $thumbnailPath);
        imagedestroy($newImage);
        break;

      case "png":
        imagepng($newImage , $thumbnailPath);
        imagedestroy($newImage);
        break;
    }
    
    
    
  }

  // サイズ変更時のアスペクト比を維持するためにオートサイズ対応
  public static function getAutoScaleChangeSize($imagePath=null , $w=0 , $h=0){
    if(!$imagePath || !is_file($imagePath)){return;}
    if(!$w && !$h){return;}

    // 元画像の取得
    $img = \mynt::exec('\lib\image\file_control','loadImage',array($imagePath));
    if(!$img){return;}

    // 元サイズの取得
    $currentSize = \mynt::exec('\lib\image\file_control','getScale',array($img));

    // 縦横の長さ割合を取得
    if($h === 0){
      $rate = $w / $currentSize["width"];
      $h = $currentSize["height"] * $rate;

    }
    else if($w === 0){
      $rate = $h / $currentSize["height"];
      $w = $currentSize["width"] * $rate;
    }

    // 元サイズよりも大きい場合は、もとに戻す
    if($w > $currentSize["width"] && $h > $currentSize["height"]){
      $w = $currentSize["width"];
      $h = $currentSize["height"];
    }


    return array(
      "width"  => $w,
      "height" => $h
    );
  }

  // 画像からサムネイルを作成する（サーバー負荷軽減策）
  public static $thumb_default_size = 300;
  public static function image2thumb($image_path="" , $thumb_path="" , $w=0 , $h=0 , $orientation=0 , $rotate=0){
    if(!$image_path || !$thumb_path){return;}
    // サムネイルファイルが作られている場合は処理しない
    if(is_file($thumb_path)){return;}
    // 変換元画像が存在しない場合は処理しない
    if(!is_file($image_path)){return;}

    // 画像サイズ判定処理
    $w = $w ? $w : self::$thumb_default_size;
    $h = $h ? $h : self::$thumb_default_size;

    // 画像アスペクト比の判定（長い方にサイズ指定の値を適用する）
    $image_size = \mynt::exec('\lib\image\data','getSize',array($image_path));
    // 縦長
    if($image_size[0] < $image_size[1]){
      $w = ($h / $image_size[1]) * $image_size[0];
    }
    // 横長
    else{
      $h = ($w / $image_size[0]) * $image_size[1];
    }

//     // 変換サイズの取得（比率自動調整）※サイズに0が入っているとautoとみなす
//     $changeSize  = self::getAutoScaleChangeSize($image_path , $w , $h);
//     if(!$changeSize){return;}
// print_r($changeSize);exit();

    // 元画像の読み込み
    $img = \mynt::exec('\lib\image\file_control','loadImage_type',array($image_path , $type_ext));
    // $newImage = imagescale($img, $changeSize["width"] , $changeSize["height"]);
    $newImage = imagescale($img, $w , $h);

    // 回転値処理orientation
    $orientation = \mynt::exec('\lib\image\data','rotate2orientation',array($orientation , $rotate));
    if($orientation && $orientation != 0 && $orientation != 1){
      $newImage = \mynt::exec('\lib\image\data','orientation',array($newImage , $orientation));
    }

    // サムネイルパスの情報取得
    $thumbPathinfo = pathinfo($thumb_path);
    if(!is_dir($thumbPathinfo["dirname"])){
      mkdir($thumbPathinfo["dirname"] , 0777 , true);
    }

    switch($thumbPathinfo["extension"]){
      case "jpg":
      case "jpeg":
        imagejpeg($newImage , $thumb_path);
        imagedestroy($newImage);
        break;

      case "png":
        imagepng($newImage , $thumb_path);
        imagedestroy($newImage);
        break;
    }

  }

  public static function image2view($image_path="" , $w=0 , $h=0 , $rotate=0){
    if(!$image_path){return;}
    // サムネイルファイルが作られている場合は処理しない
    // if(is_file($thumb_path)){return;}
    // 変換元画像が存在しない場合は処理しない
    if(!is_file($image_path)){return;}

    // 画像サイズ判定処理
    // $w = $w ? $w : self::$thumb_default_size;
    // $h = $h ? $h : self::$thumb_default_size;

    // // 画像アスペクト比の判定（長い方にサイズ指定の値を適用する）
    // $image_size = \mynt::exec('\lib\image\data','getSize',array($image_path));
    // // 縦長
    // if($image_size[0] < $image_size[1]){
    //   $w = ($h / $image_size[1]) * $image_size[0];
    // }
    // // 横長
    // else{
    //   $h = ($w / $image_size[0]) * $image_size[1];
    // }

//     // 変換サイズの取得（比率自動調整）※サイズに0が入っているとautoとみなす
//     $changeSize  = self::getAutoScaleChangeSize($image_path , $w , $h);
//     if(!$changeSize){return;}
// print_r($changeSize);exit();

// $exif = exif_read_data($image_path, 0, true);
// print_r($exif);exit();
    // 画像データを読み込んでexifモードを取得する
    $exif = exif_read_data($image_path , 'IFD0');
// print_r($exif);exit();
    $orientation = isset($exif["Orientation"]) ? $exif["Orientation"] : 0;

    // 元画像の読み込み
    $img = \mynt::exec('\lib\image\file_control','loadImage',array($image_path));
    // $newImage = imagescale($img, $changeSize["width"] , $changeSize["height"]);

    list($w , $h) = \mynt::exec('\lib\image\data','deg2size',array($orientation , $rotate , $w , $h));

    // 画像サイズの縦横大きい方を取得
    $max_size = $w > $h ? (int)$w : (int)$h;

    // 画像サイズが既定値よりも大きい場合はrateを取得する。
    $rate = 1200 / $max_size;

    // 画像サイズの調整
    if($rate < 1){
      $w = $w * $rate;
      $h = $h * $rate;
    }


    $newImage = imagescale($img, $w , $h);
// die("w : ".$w." / h : ".$h ." / rate : ".$rate." / max : ".$max_size);
    // 回転値処理orientation
    $orientation = \mynt::exec('\lib\image\data','rotate2orientation',array($orientation , $rotate));
    if($orientation && $orientation != 0 && $orientation != 1){
      $newImage = \mynt::exec('\lib\image\data','orientation',array($newImage , $orientation));
    }

    // サムネイルパスの情報取得
    $pathinfo = pathinfo($image_path);
    // if(!is_dir($thumbPathinfo["dirname"])){
    //   mkdir($thumbPathinfo["dirname"] , 0777 , true);
    // }

    $contentType = \mynt::exec('\lib\image\file_control','ext2contentType',array($pathinfo["extension"]));
    header('Content-Type: '.$contentType);

    switch($pathinfo["extension"]){
      case "jpg":
      case "jpeg":
        // imagejpeg($newImage , $image_path);
        // imagedestroy($newImage);
        // $jpeg = imagecreatefromjpeg($url);
        imagejpeg($newImage);
        break;

      case "png":
        // imagepng($newImage , $image_path);
        // imagedestroy($newImage);
        // $png = imagecreatefrompng($url);
        imagepng($newImage);
        break;
    }

    
  }



}