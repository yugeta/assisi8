<?php
namespace lib\common;

class scanpath{

  // 任意のディレクトリ内を検索する（1階層のみ）
  // 拡張子複数指定可能
  // return @ type [dir,file] , dirname , basename , *filename(fileのみ) , *extension(fileのみ)
  public static function scanFiles($path="",$exts=array()){
    if(!$path || !is_dir($path)){return array();}
    $files = array_diff(scandir($path) , array("..","."));
    
    $arr   = array();
    $names = array();
    foreach($files as $file){
      // if(!preg_match("/^(.*?)\.".$ext."$/" , $file , $match)){continue;}
      
      $pathinfo = pathinfo($file);
      if($exts && !in_array($pathinfo["extension"] , $exts)){continue;}
  
      $pathinfo["type"] = (is_dir($path."/".$file)) ? "dir" : "file";
      if(is_dir($path."/".$file)){
        unset($pathinfo["filename"]);
      }
      $pathinfo["dirname"] = $path;
      $arr[]   = $pathinfo;
    }
    return $arr;
  }

  // 多重階層検索
  

}