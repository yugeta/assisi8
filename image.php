<?php
// namespace mynt;
/**
 * MYNT Studio : Image-view
 * Author  : Yugeta.Koji
 * Date    : 2019.08.30 
 * Version : 1.0 (2019.08.30)
 * 
 * [Spec]
 * 
 * - common
 * b @ base64-data
 * 
 * - image-path
 * u @ user-id
 * g @ group-id
 * i @ image-id
 * 
 * - size
 * s @ size（正方形の一変）
 * w @ width（横サイズ）
 * h @ height（縦サイズ）
 * 
 * - other
 * o @ Orientation (default:1)
 * r @ Rotate [0 , 90 , 180 , 270]
 * e @ Extension
 * 
 * [Example]
 * base64 : {user-id , group-id , image-id } -> {"u":"*","g":"*","i":"*"}
 * ex) {"u":1,"g":1,"i":4}
 */


require_once "lib/main/init.php";
class mynt extends \lib\main\init {}

// クエリパラメータのデコード処理 ( q -> $_GET[key=value] )
\mynt::queryParameters();



// // デバッグ用
// if(isset($_GET["mode"]) && $_GET["mode"] === "debug"){
//   $query = \mynt::exec('\lib\image\thumbnail','base64_decode',array($_GET["b"]));
// }

// // サムネイル表示
// else if(isset($_GET["b"]) && $_GET["b"]){

//   // クエリデータからbase64->dataを取得
//   $data = \mynt::exec('\lib\image\thumbnail','query2base64',array($_GET["b"]));
//   if($data === false){
//     exit();
//   }

//   // テンプレートファイルの確認
//   if(\mynt::exec('\lib\image\thumbnail','checkTemplate',array($_GET["b"] , $data)) === true){
//     \mynt::exec('\lib\image\thumbnail','viewThumbnail',array($_GET["b"] , $data));
//   }

//   // テンプレートに無い場合、生成
//   else{
//     \mynt::exec('\lib\image\thumbnail','saveThumbnail',array($_GET["b"] , $data));
//     \mynt::exec('\lib\image\thumbnail','viewThumbnail',array($_GET["b"] , $data));
//   }
  
// }




// gitpage用
// queryからファイルパスを取得して、スケールチェンジしたthumbnailを表示する
if(isset($_GET["mode"])){
  switch($_GET["mode"]){
    case "view":
      if(isset($_GET["file"]) && $_GET["file"] && is_file($_GET["file"])){
        echo "<img src='".$_GET["file"]."'>";
      }
      break;

    // ex) ?mode=thumbnail&file=data/media/aaa.jpg&w=200
    // thumbname : (dir)/(file).(w).(h).(ext)
    case "thumbnail":
      if(isset($_GET["file"])&& $_GET["file"] && is_file($_GET["file"])){
        \mynt::exec('\lib\image\thumbnail','view',array($_GET["file"]));
      }
      break;
    
    case "git":
      $ext = $_GET["ext"];
      if($ext == "jpg"){
        $ext = "jpeg";
      }
      $img = \mynt::exec('\page\gitpage\git','getFileInner_binary',array($_GET["uid"],$_GET["id"],$_GET["path"],$ext,$_GET["branch"]));
      header('Content-Type: image/'.$ext);
      echo $img;
      break;
  }
}

// smful用
// サムネイル表示
else if(isset($_GET["b"]) && $_GET["b"]){

  if(isset($_GET["debug"]) && $_GET["debug"] === "init"){
    echo "Debug : init : ".$_GET["b"];
    exit();
  }

  // クエリデータからbase64->dataを取得
  $data = \mynt::exec('\page\smful\php\thumbnail','query2base64',array($_GET["b"]));
  if($data === false){
    exit();
  }

  // [Debug] --
  if(isset($_GET["debug"]) && $_GET["debug"] === "data-view"){
    echo "Debug : data-view : ";
    print_r($data);
    exit();
  }

  // check-thuimnail
  $thumb_path = \mynt::exec('\page\smful\php\thumbnail','getFilepath',array($_GET["b"] , $data));

  // make-thumbnail
  if(!is_file($thumb_path)){

    // image-path
    $image_path = \mynt::exec('\page\smful\php\thumbnail','getNativeImagePath' , array($data));

    \mynt::exec('\lib\image\thumbnail' , 'image2thumb' , array($image_path , $thumb_path , $data["w"] , $data["h"] , $data["o"] , $data["r"]));
  }

  // view-thumbnail
  if(is_file($thumb_path)){
    \mynt::exec('\lib\image\view' , 'file' , array($thumb_path));
  }

  // // テンプレートファイルの確認
  // if(\mynt::exec('\page\smful\php\thumbnail','checkTemplate',array($_GET["b"] , $data)) === true){
  //   \mynt::exec('\page\smful\php\thumbnail','viewThumbnail',array($_GET["b"] , $data));
  // }

  // // テンプレートに無い場合、生成
  // else{
  //   \mynt::exec('\page\smful\php\thumbnail','saveThumbnail',array($_GET["b"] , $data));
  //   \mynt::exec('\page\smful\php\thumbnail','viewThumbnail',array($_GET["b"] , $data));
  // }
  
}

// 画像調整表示(smful用）)
else if(isset($_GET["smful"]) && $_GET["smful"]){

  // クエリデータからbase64->dataを取得
  $data = \mynt::exec('\page\smful\php\thumbnail','query2base64',array($_GET["smful"]));
  if($data === false){exit();}

  // [Debug] --
  if(isset($_GET["debug"]) && $_GET["debug"] === "data-view"){
    echo "Debug : data-view : ";
    print_r($data);
    exit();
  }

  // // check-thuimnail（同じtmp画像があれば、処理しない）
  // $tmp_path = \mynt::exec('\page\smful\php\thumbnail','getFilepath',array($_GET["smful"] , $data));

  // make-thumbnail
  // if(!is_file($tmp_path)){

    // image-path
    $image_path = \mynt::exec('\page\smful\php\thumbnail','getNativeImagePath' , array($data));

    // \mynt::exec('\lib\image\thumbnail' , 'image2thumb' , array($image_path , $tmp_path , $data["w"] , $data["h"] , $data["o"] , $data["r"]));
// print_r($data);exit();
    \mynt::exec('\lib\image\thumbnail' , 'image2view' , array($image_path , $data["w"] , $data["h"] , $data["r"]));
  // }

  // // view-thumbnail
  // if(is_file($tmp_path)){
  //   \mynt::exec('\lib\image\view' , 'file' , array($tmp_path));
  // }
  
}



