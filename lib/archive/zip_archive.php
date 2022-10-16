<?php
namespace lib\archive;

class zip_archive{

  // 1ファイル（フォルダ）を同一階層にアーカイブする処理
  public static function make($target_path="" , $save_dir="" , $rm_flg=false){
    if(!self::check_target_dir($target_path)){
      return false;
    }
    if($save_dir && self::check_save_dir($save_dir)){
      // return false;
    }
    $archive_path = self::archive($target_path);
    self::move_archive($target_path , $save_dir);

    if($rm_flg === true){
      self::base_remove($target_path);
    }
    
    return true;
  }

  // 対象ファイルを検索
  public static function check_target_dir($target_path=""){
    if(!$target_path){
      return null;
    }
    else if(is_file($target_path) || is_dir($target_path)){
      return true;
    }
    else{
      return false;
    }
  }

  // 保存場所を検索（無い場合は作成する）
  public static function check_save_dir($save_dir="" , $mode="make"){
    if(!$save_dir){
      return null;
    }
    else if(is_dir($save_dir)){
      return true;
    }
    else if($mode==="make"){
      mkdir($save_dir , 0744 , true);
      return true;
    }
    return false;
  }

  // アーカイブ処理（同一階層に保持）
  public static function archive($target_path=""){
    if(is_file($target_path)){
      return self::archive_file($target_path);
    }
    else if(is_dir($target_path)){
      return self::archive_dir($target_path);
    }
    return false;
  }

  public static function archive_dir($target_path=""){
    $pathinfo = pathinfo($target_path);
    $sp       = explode("/" , $pathinfo["dirname"]);
    $name     = $sp[count($sp)-1];
    $cmd  = "cd ". $target_path .";";
    $cmd .= "zip -rm ../". $pathinfo["filename"] .".zip .";
    exec($cmd);
  }
  public static function archive_file($target_path=""){
    $pathinfo = pathinfo($target_path);
    $ext      = $pathinfo["extension"] === "zip" ? ".".date("YmdHis").".zip" : ".zip";
    $cmd  = "cd ". $pathinfo["dirname"] .";";
    $cmd .= "zip -rm ". $pathinfo["filename"]. $ext ." ".$target_path;
    exec($cmd);
  }

  public static function move_archive($target_path="" , $save_dir=""){
    if(!$target_path || !$save_dir){return;}
    if(!is_dir($save_dir)){return;}

    $pathinfo = pathinfo($target_path);
    $base_path = "";
    if(is_dir($target_path)){
      $base_path = $pathinfo["filename"].".zip";
    }
    else if(is_file($target_path)){
      $ext       = $pathinfo["extension"] === "zip" ? ".".date("YmdHis").".zip" : ".zip";
      $base_path = $pathinfo["filename"]. $ext;
    }

    $save_path = $save_dir.$base_path;
    if(is_file($save_dir.$base_path)){
      $flname = preg_replace("/\.zip$/" , "_".date("YmdHis").".zip" , $base_path);
      $save_path = $save_dir.$flname;
    }

    if(!$base_path || !is_file($pathinfo["dirname"]."/".$base_path)){return;}
    rename($pathinfo["dirname"]."/".$base_path , $save_path);

  }
  public static function base_remove($target_path=""){
    if(!$target_path){return;}
    if(is_file($target_path)){
      unlink($target_path);
    }
    else if(is_dir($target_path)){
      rmdir($target_path);
      // unset($res);
      // exec("ls ".$target_path , $res);
      // if(!implode("",$res)){
      //   exec("rm ".$target_path);
      // }
    }
  }

}
