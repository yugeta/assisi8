<?php
namespace lib\media;

class data{
  public static function remove($filepath=""){
    if(!$filepath || !is_file($filepath)){return '{"status":"error","type":0,"message":"'.$filepath."/".is_file($filepath).'"}';}
    
    $dir = \mynt::exec('\lib\media\common','getDir');
    if(!is_dir($dir)){return '{"status":"error","message":1}';}

    $pathinfo = pathinfo($filepath);
    $id = $pathinfo["filename"];
    $filename = $id .".". $pathinfo["extension"];

    // $info = \mynt::exec('\lib\media\common','getInfo',array($filename));
    $where = array(
      "currentName" => $filename
    );

    $info = \mynt::data_load($GLOBALS["config"]["data"] , 'lib_media' , array() , $where);
    if(!$info){return '{"status":"error","type":2,"message":"'.$filename.'","res":"'.json_encode($info).'"}';}

    // del
    $res_del = \mynt::data_del($GLOBALS["config"]["data"] , 'lib_media' , array() , $where);
    // $res_del = \mynt::exec('\lib\media\common','delInfo',array($filename));


    // $path = $dir . $filename.".".$info["extension"];
    unlink($filepath);

    return '{"status":"ok","id":"'.$id.'"}';
    // return $path;
  }

  
}