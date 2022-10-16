<?php
namespace lib\image;

class data{
  public static function orientation($img=null , $orientation=0){
    switch($orientation){
			case 0: // normal
			case 1: // normal
        break;
        
			case 2: // flip width
				imageflip($img, IMG_FLIP_HORIZONTAL);
        break;
        
			case 3: // rotate 180deg
				$col = imagecolorallocate($img, 0, 0, 0);
				$img = imagerotate($img, -180 , $col);
        break;
        
			case 4: // flip height
				imageflip($img, IMG_FLIP_VERTICAL);
        break;
        
			case 5: // flip width + rotate 270deg
				imageflip($img, IMG_FLIP_HORIZONTAL);
				$col = imagecolorallocate($img, 0, 0, 0);
				$img = imagerotate($img, -90 , $col);
        break;
        
			case 6: // rotate 90deg
				$col = imagecolorallocate($img, 0, 0, 0);
				$img = imagerotate($img, -90 , $col);
        break;
        
			case 7: // flip height + rotate 90deg
				imageflip($img, IMG_FLIP_VERTICAL);
				$col = imagecolorallocate($img, 0, 0, 0);
				$img = imagerotate($img, -90 , $col);
        break;
        
			case 8: // rotate 270deg
				$col = imagecolorallocate($img, 0, 0, 0);
				$img = imagerotate($img, -270 , $col);
				break;
    }
    return $img;
  }

  public static function rotate2orientation($orientation , $rotate){
    switch($orientation){
			
      case 2: // flip width
        switch($rotate){
          case 90  : $orientation = 5; break;
          case 180 : $orientation = 4; break;
          case 270 : $orientation = 7; break;
        }
        break;
      case 3: // rotate 180deg
        switch($rotate){
          case 90  : $orientation = 8; break;
          case 180 : $orientation = 0;break;
          case 270 : $orientation = 6;break;
        }
        break;
      case 4: // flip height
        switch($rotate){
          case 90  : $orientation = 7; break;
          case 180 : $orientation = 2; break;
          case 270 : $orientation = 5; break;
        }
        break;
      case 5: // flip width + rotate 270deg
        switch($rotate){
          case 90  : $orientation = 4; break;
          case 180 : $orientation = 7; break;
          case 270 : $orientation = 2; break;
        }
        break;
      case 6: // rotate 90deg
        switch($rotate){
          case 90  : $orientation = 3; break;
          case 180 : $orientation = 8; break;
          case 270 : $orientation = 1; break;
        }
        break;
      case 7: // flip height + rotate 90deg
        switch($rotate){
          case 90  : $orientation = 2; break;
          case 180 : $orientation = 5; break;
          case 270 : $orientation = 4; break;
        }
        break;
      case 8: // rotate 270deg
        switch($rotate){
          case 90  : $orientation = 0; break;
          case 180 : $orientation = 6; break;
          case 270 : $orientation = 3; break;
        }
        break;

      case 0: // normal
      case 1: // normal
      default:
        switch($rotate){
          case 90  : $orientation = 6; break;
          case 180 : $orientation = 3; break;
          case 270 : $orientation = 8; break;
        }
        break;
    }
    return $orientation;
  }

  public static function getSize($image_path=""){
    if(!$image_path){return null;}
    if(!is_file($image_path)){return null;}
    return getimagesize($image_path);
  }

  public static function getWH($image_path=""){
    if(!$image_path){return null;}
    if(!is_file($image_path)){return null;}
    $img = \mynt::exec('\lib\image\file_control','loadImage',array($image_path));
    return array(
      "width"  => imagesx($img),
      "height" => imagesy($img)
    );
  }

  // 回転値が90,270の場合は、画面サイズが入れ替わる処理
  public static function deg2size($orientation , $rotate , $w , $h){
    $deg_id = self::rotate2orientation($orientation , $rotate);
// die("orientation : ".$orientation . " / rotate : ".$rotate ." / deg-id : ".$deg_id);

    // if($orientation || $rotate){
    //   if($deg_id == 6 || $deg_id == 8){
    //     return array($h , $w);
    //   }
    // }
    if($rotate == 90 || $rotate == 270 || $deg_id == 6 || $deg_id == 8){
      return array($h , $w);
    }
    return array($w , $h);
  }
}