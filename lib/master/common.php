<?php
namespace lib\master;

class common{

  public static function csv_download(){
    if(!isset($_POST["heads"]) || !isset($_POST["datas"])){return;}
    $heads = json_decode($_POST["heads"] , true);
    
    $csv = "";
    $head_arr = array();
    for($i=0; $i<count($heads); $i++){
      $head_arr[] = '"'.$heads[$i].'"';
    }
    $csv .= implode(",",$head_arr)."\n";

    $datas = json_decode($_POST["datas"] , true);
    for($i=0; $i<count($datas); $i++){
      $data_arr = array();
      if(!$datas){continue;}
      for($j=0; $j<count($datas[$i]); $j++){
        $data_arr[] = '"'.$datas[$i][$j].'"';
      }
      $csv .= implode(",",$data_arr)."\n";
    }

    return $csv;
  }
}