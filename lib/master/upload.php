<?php
namespace lib\master;

class upload{
  public static function csv($page_name="",$table_name=""){
    if(!$table_name){return;}

    // dbのセッティングデータの読み込み
    $setting_data = \mynt::exec('\lib\master\db','getData_tableSetting',array($page_name , $table_name));
    if(!$setting_data){return;}

    // 現在DBに登録されているデータを呼び出し
    $current_datas = array();
    $res = \mynt::data_load($page_name , $table_name);
    if($res["status"] === "ok"){
      foreach($res["data"] as $data){
        $current_datas[$data["id"]] = $data;
      }
    }

    // アップロードファイルを開く(csv) -> csvデータの分解
    if(!$_FILES["csv"]["tmp_name"] || !is_file($_FILES["csv"]["tmp_name"])){return;}
    // $csv_data = file_get_contents($_FILES["csv"]["tmp_name"]);
    $datas = \mynt::exec('\lib\string\csv','loadFile',array($_FILES["csv"]["tmp_name"]));
    if(!count($datas)){return;}

    // 1行目をヘッダ情報として抜き出す
    $headers = $datas[0];
    $max_id_num = 0;

    // データ上書き
    for($i=1; $i<count($datas); $i++){
      if(!$datas[$i]){continue;}
      $lineData = array();
      for($j=0; $j<count($datas[$i]); $j++){
        if($headers[$j] === ""){continue;}
        if(!isset($setting_data["columns"][$headers[$j]])){continue;}
        $val = $datas[$i][$j];
        switch($setting_data["columns"][$headers[$j]]["type"]){
          case "INT":
            $val = preg_replace('/[^0-9\-]/' , '' , $val);
            break;
          case "FLOAT":
            $val = preg_replace('/[^0-9\.\-]/' , '' , $val);
            break;
        }
        $lineData[$headers[$j]] = $val;
      }
      $current_datas[$lineData["id"]] = $lineData;
      $max_id_num = $max_id_num < (int)$lineData["id"] ? (int)$lineData["id"] : $max_id_num;
    }

    
    $entry = date("YmdHis");
    foreach($current_datas as $id => $data){
      $data["id"] = $id;
      $data["entry"] = $entry;
      \mynt::data_save($page_name , $table_name , $data);
    }

    if(isset($_POST["redirect"]) && $_POST["redirect"]){
      \mynt::exec('\lib\url\common','setUrl',array($_POST["redirect"]));
    }
  }
}