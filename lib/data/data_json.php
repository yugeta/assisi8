<?php
namespace lib\data;
/**
 * Path    : lib/php/data_json.php
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.1.0
 * Summary : data/##.jsonをデータベースとして利用
 * Example : 
 */

class data_json{

  public static function data_save($dbname="" , $table="" , $datas=array() , $wheres=array()){
//print_r($wheres);exit();
    // 型とデフォルト値のセット（設定にない値は排除）
    $datas = self::setStrings($table , $datas);
    if(!$datas){
      return array(
        "status"  => "error",
        "message" => "no-datas.",
        "datas"    => $datas
      );
    }
    $config  = \mynt::exec("\\lib\\data\\database" , "getSetting");
    $dir     = \mynt::exec("\\lib\\data\\database" , "getDir");
    $dbname  = ($dbname) ? $dbname : $config["database"];
    $basedir = $dir.$dbname ."/";
    if(!is_dir($basedir)){
      mkdir($basedir , 0777 , true);
    }
    $basepath = $basedir.$table;

    // index-check
    $indexPath_where = (string)self::getIndexPath($table , $wheres);
    $indexPath_datas = (string)self::getIndexPath($table , $datas);
// print_r(array_merge($datas,$wheres));
// echo $indexPath_where."<br>".$indexPath_datas."<br>".$basepath;
// exit();

    // indexpathの書き換え
    $indexPath = "";
    // データのみに、指定がある場合
    if($indexPath_where && !$indexPath_datas){
      $indexPath = $indexPath_where;
    }
    // whereのみに指定がある場合
    else if(!$indexPath_where && $indexPath_datas){
      $indexPath = $indexPath_datas;
    }
    // dataとwhereのどちらも指定があり、同じ値でない場合
    else if($indexPath_where && $indexPath_datas
    && $indexPath_where !== $indexPath_datas){
      $file_where = $basepath . "/". $indexPath_where .".json";
      $file_datas = $basepath . "/". $indexPath_datas .".json";
      if(is_file($file_where)){
        rename($file_where , $file_datas);
      }
      $indexPath = $indexPath_datas;
    }
    else if($indexPath_where && $indexPath_datas
    && $indexPath_where === $indexPath_datas){
      $indexPath = $indexPath_datas;
    }


    if($indexPath!==""){
      if(!is_dir($basepath)){
        mkdir($basepath , 0777 , true);
      }
      // $path = $indexPath .".json";
      $path = $basepath . "/". $indexPath .".json";

      if(!is_dir(dirname($path))){
        mkdir(dirname($path) , 0777 , true);
      }
      return self::data_save_file($path,$table,$datas,$wheres);
    }
    else{
      $path = $basepath . ".json";
      return self::data_save_file($path,$table,$datas,$wheres);
    }
  }

  public static function data_save_file($path="",$table="",$datas=array(),$wheres=array()){
    $config = \mynt::exec("\\lib\\data\\database" , "getSetting");
    $saveDatas = array();
    // 更新の場合は、事前に対象データを呼び出して作業する。
    if($wheres){
      $beforeData = self::data_load("",$table,array(),$wheres);
      if($beforeData["status"] === "error"){
        $datas = self::remove_blank_id_value($datas);  // id値のブランクは削除する
        array_push($saveDatas , json_encode($datas , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
      }
      else{
        for($i=0; $i<count($beforeData["data"]); $i++){
          foreach($datas as $d_key => $d_val){
            $beforeData["data"][$i][$d_key] = $d_val;
          }
          // indexのファイル名が上書きされてしまう事象のための処理
          foreach($wheres as $d_key2 => $d_val2){
            if(isset($beforeData["data"][$i][$d_key2])){continue;}
            $beforeData["data"][$i][$d_key2] = $wheres[$d_key2];
          }
          // $beforeData["data"][$i] = array_merge($beforeData["data"][$i] , $wheres);
          $beforeData["data"][$i] = self::remove_blank_id_value($beforeData["data"][$i]);  // id値のブランクは削除する
          array_push($saveDatas , json_encode($beforeData["data"][$i] , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
      }
    }
    else{
      $datas = self::remove_blank_id_value($datas);  // id値のブランクは削除する
      array_push($saveDatas , json_encode($datas , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    // 保存ファイルの変更確認


    // dir-check
    if(!is_dir(dirname($path))){
      mkdir(dirname($path) , 0777 , true);
    }

    $setting_table = \mynt::exec("\\lib\\data\\database" , "getTableSetting" , array($table));
// print_r($saveDatas);exit();

    // 追記型
    if(isset($setting_table["info"]["type"])
    && $setting_table["info"]["type"] === "add"){
      for($i=0; $i<count($saveDatas); $i++){
        file_put_contents($path , $saveDatas[$i]."\n" , FILE_APPEND);
      }
    }

    // 上書き型
    else if(isset($setting_table["info"]["type"])
    && $setting_table["info"]["type"] === "overwrite"){
      $texts = is_file($path) ? file_get_contents($path) : "";
      $lists = explode("\n",$texts);
      for($i=0; $i<count($saveDatas); $i++){
        $data = json_decode($saveDatas[$i] , true);
        // 既存データに上書き
        if(isset($data["id"]) && $data["id"]){
          if(strtoupper($setting_table["columns"]["id"]["type"]) === "INT"){
            $id = (int)$data["id"];
            unset($data["id"]);
            $lists[$id-1] = json_encode($data , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
          }
          else{
            $flg = null;
            if($lists && count($lists)){
              for($j=count($lists)-1; $j>=0; $j--){
                $json = json_decode($lists[$j] , true);
                if($json["id"] !== $data["id"]){continue;}
                $flg = $j;
                break;
              }
            }
            if($flg !== null){
              $lists[$flg] = $saveDatas[$i];
            }
            else{
              array_push($lists , $saveDatas[$i]);
            }
          }
        }
        
        // 追記
        else{
          array_push($lists , $saveDatas[$i]);
        }
      }
      $lists = array_filter($lists);
      file_put_contents($path , implode("\n",$lists)."\n");
    }

    // 標準型(static)
    else{
      for($i=0; $i<count($saveDatas); $i++){
        file_put_contents($path , $saveDatas[$i]);
      }
    }

    // AUTO_INCREMENTの値を取得（ファイルの最終行数の取得）
    unset($res);
    exec("cat ".$path ." |wc -l" , $res);

    $res_datas = array(
      "status" => "ok",
      "count"  => count($saveDatas),
      // "data"   => array_merge($datas,$wheres),
      "data"   => $datas,
      "path"   => $path
    );
// print_r($res_datas);exit();
    // if(isset($setting_table["info"]["type"])
    // && $setting_table["info"]["type"] === "overwrite"
    // && isset($data["id"]) && $data["id"]){
    //   $res_datas["data"]["auto_increment"] = $data["id"];
    //   $res_datas["data"]["id"] = $data["id"];
    // }
    // else{
    //   $linesCount = (count($res) || implode("",$res)) ? (int)$res[0] : null;
    //   $res_datas["data"]["auto_increment"] = $linesCount;
    //   $res_datas["data"]["id"] = $linesCount;
    // }
    if(isset($res_datas["data"]["id"])){
      $res_datas["data"]["auto_increment"] = $res_datas["data"]["id"];
    }
    else{
      $linesCount = (count($res) || implode("",$res)) ? (int)$res[0] : null;
      $res_datas["data"]["auto_increment"] = $linesCount;
      $res_datas["data"]["id"] = $linesCount;
    }

    return $res_datas;
  }

  // id値のブランクは削除する
  public static function remove_blank_id_value($arr=array()){
    if(!$arr){return $arr;}
    if(isset($arr["id"]) && $arr["id"] === ""){unset($arr["id"]);}
    return $arr;
  }


  public static function data_load($dbname="",$table="",$keys=array(),$wheres=array(),$sort=array()){
    if(!$table){
      return array("status"=>"error","data"=>null,"message"=>"tableの指定がありません。 : ".json_encode(func_get_args()));
    }

    $config = \mynt::exec('\lib\data\database' , "getSetting");
    $dir    = \mynt::exec('\lib\data\database' , "getDir");
    $table_setting = \mynt::exec("\\lib\\data\\database" , "getTableSetting" , array($table));
    

    $indexPath  = self::getIndexPath($table , $wheres);
    $pkeys      = self::getPrimaryKeys($table);
    $increments = self::getAutoincrementKeys($table);
    $dbname     = ($dbname) ? $dbname : $config["database"];
    $basepath   = $dir . $dbname ."/". $table;
// if($table==="account"){
//   echo "indexPath : ".$indexPath.PHP_EOL;
//   print_r($wheres);exit();
// }

    if($table_setting
    && isset($table_setting["info"])
    && isset($table_setting["info"]["type"])
    && $table_setting["info"]["type"] === "static"){
      $static_path = $indexPath ? $basepath."/".$indexPath : $basepath;
      return self::data_load_static($static_path . ".json");
    }


    $data = array();
    if($indexPath){
      $path = $basepath."/".$indexPath.".json";
      if(is_file($path)){
        $data = self::data_load_file($path,$keys,$wheres,$pkeys,$increments);
      }
    }
    else{
      $path = $basepath . ".json";
// echo $path;
// echo $basepath.",";
      if(is_dir($basepath)){
        exec("find ".$basepath." -type f" , $files);
        $path = array();
        for($i=0; $i<count($files); $i++){
          $res = self::data_load_file($files[$i],$keys,$wheres,$pkeys,$increments);
          if(!$res || !count($res)){continue;}
          $data = array_merge($data , $res);
          array_push($path , $files[$i]);
        }
      }
      else if(is_file($path)){
        $data = self::data_load_file($path,$keys,$wheres,$pkeys,$increments);
      }
    }

    if(count($data)){
      // sort
      if($sort){
        $data2 = self::array_msort($data , $sort);
        return array("status"=>"ok","data"=>$data2);
      }
      else{
        return array("status"=>"ok","data"=>$data);
      }
    }
    else{
      return array("status"=>"error","message"=>"no-data.","code"=>"json.data.002","data"=>$data,"file-path"=>$path);
    }
  }

  public static function data_load_static($path){
    if(!is_file($path)){
      return array(
        "status"    => "error",
        "message"   => "not-json-file.",
        "file-path" => $path
      );
    }
    $data = json_decode(file_get_contents($path) , true);
    if($data){
      return array(
        "status"    => "ok",
        "data"      => $data,
        "file-path" => $path
      );
    }
    else{
      return array(
        "status"    => "error",
        "message"   => "not-data.",
        "file-path" => $path
      );
    }
  }



  // normal-load
  public static function data_load_file($path="" , $keys=array() , $wheres=array() , $pkeys=array() , $increments=array()){
    if(!is_file($path)){
      return array();
    }
    if(!preg_match("/\.json/" , $path)){
      return;
    }
// echo $path;
    $lines   = explode("\n",file_get_contents($path));
    $resData = array();
    $cache   = array();
// echo "lines : ".count($lines);
    for($i=count($lines)-1; $i>=0; $i--){
      if(!$lines[$i]){continue;}
// die($lines[$i]);
      $json = json_decode($lines[$i],true);

      // increments : 自動採番に関するレコードに行番号を付与する(id)
      if($increments){
// echo count($increments).",";
        for($inc=0; $inc<count($increments); $inc++){
          if(!isset($increments[$inc]) || !$increments[$inc]){continue;}
          $key = $increments[$inc];
// echo $key.",";
          if(!isset($json[$key])){
            $json[$key] = $i+1;
            // echo $key;
          }
        }
      }

      // primary-keys
      $key = "";

      if($pkeys){
        for($p=0; $p<count($pkeys); $p++){
          if(!$pkeys[$p] || !isset($json[$pkeys[$p]])){continue;}
          $key .= $json[$pkeys[$p]];
        }
      }

      if(!$key){continue;}
      if(isset($cache[$key])){continue;}
      $cache[$key] = true;
      if(isset($json["flg"]) && $json["flg"] == 1){continue;}

      if($wheres){
        $flg = true;
        foreach($wheres as $w_key => $w_val){
          if(!isset($json[$w_key]) || $json[$w_key] != $w_val){
            $flg = false;
            break;
          }
        }
        if($flg === false){continue;}
      }

      if($keys){
        $newData = array();
        for($j=0; $j<count($keys); $j++){
          if(!isset($json[$keys[$j]])){continue;}
          $newData[$keys[$j]] = $json[$keys[$j]];
        }
        array_push($resData , $newData);
      }
      else{
        array_push($resData , $json);
      }
    }

    return $resData;
  }


  public static function data_del($dbname="",$table="",$datas=array(),$wheres=array()){
    $res = self::data_load($dbname,$table,array(),$wheres);
    if($res["status"] === "error"){return;}

    if($datas){
      foreach($datas as $key => $val){
        $wheres[$key] = $val;
      }
    }
    $wheres["flg"] = 1;

    $setting = \mynt::exec('\lib\data\database','getTableSetting',array($table));

    // 追記型
    if($setting["info"]["type"] === "add"){
      $res_array = array(
        "status" => "ok",
        "data"=>array()
      );
      foreach($res["data"] as $data){
        $wheres["id"] = $data["id"];
        $res = self::data_save($dbname,$table,$wheres,array());
        if($res["status"] === "error"){continue;}
        $res_array["data"][] = $res["data"];
      }
      return $res_array;
    }

    // 上書き型
    else if($setting["info"]["type"] === "overwrite"){
      $res_array = array(
        "status" => "ok",
        "data"=>array()
      );
      foreach($res["data"] as $data){
        $data["flg"] = 1;
        $res = self::data_save($dbname,$table,$data,array());
        if($res["status"] === "error"){continue;}
        $res_array["data"][] = $res["data"];
      }
      return $res_array;
    }

    // 標準型(static)
    else{
      return self::data_save($dbname,$table,$wheres,array());
    }
  }






  // index対応の場合の階層取得
  // 2021.09.09 : index名が変更になった場合に、ファイル名の変更を自動で行う処理 : $where(元index) -> $data(変更index)
  public static function getIndexPath($table="" , $datas=array()){
    if(!$table || !$datas){return "";}
    // $config = 
    $keys = \mynt::exec('\lib\data\common',"getIndexKeys",array($table));
// print_r($keys);exit();
    $newDatas = array();
    if($keys){
      foreach($keys as $key => $arr){
        $val_datas = array();
        for($i=0; $i<count($arr); $i++){
          $default_string = \mynt::exec('\lib\data\common','getTableColumns_defaultString',array($table , $arr[$i]));
          $val_datas[] = (isset($datas[$arr[$i]])) ? $datas[$arr[$i]] : $default_string;
        }
        array_push($newDatas , implode("_",$val_datas));
      }
    }

    return implode("/",$newDatas);
  }


  // 設定データのprimary-keyを元に、id値を算出する
  public static function getPrimaryKeys($table_name){
    $setting = \mynt::exec('\lib\data\database' , "getSetting" , array());
    $tables  = \mynt::exec('\lib\data\database' , "getTableSetting" , array($table_name));
    if(!isset($tables["columns"])){return;}
    $keys = array();
    foreach($tables["columns"] as $key => $val){
      if(!isset($val["option"])
      || !$val["option"]
      || !strstr(strtolower($val["option"]),"primary key")){continue;}
      array_push($keys , $key);
    }
    return $keys;
  }
  
  public static function getAutoincrementKeys($table_name){
    $tables  = \mynt::exec("\\lib\\data\\database" , "getTableSetting" , array($table_name));
    if(!isset($tables["columns"])){return;}
    $keys = array();
    foreach($tables["columns"] as $key => $val){
      if(!isset($val["option"])
      || !$val["option"]
      || !strstr(strtolower($val["option"]),"auto_increment")){continue;}
      array_push($keys , $key);
    }
    return $keys;
  }














  // // check-init : dataフォルダの存在確認
  // public static function checkInit($config=""){
  //   if(!$config || !isset($config["dir"])){
  //     die("Error (code:init-001) No Config.");
  //   }

  //   // アクセスチェック
  //   return is_dir($config["dir"]."config/");
  // }

  // Load-install-Config
	public static function loadInstallConfig($configDir=""){
    $dir = $configDir;
		if(!is_dir($dir)){return;}
		$data  = array();
		$jsons = scandir($dir);
		for($j=0, $d=count($jsons); $j<$d; $j++){
			if(!preg_match("/(.+?)\.json$/", $jsons[$j] , $match)){continue;}
			$str    = file_get_contents($dir . $jsons[$j]);
			$str    = \mynt\lib\tag::conv($str); //設定値の置き換え処理
			$config = json_decode($str , true);
			if(isset($config["flg"]) && $config["flg"] === "1"){continue;}
			$data[$match[1]] = $config;
    }
		return $data;
  }
  


  // database-ファイル操作

  public static function create_database(){
    $dir    = \mynt::exec("\\lib\\data\\database" , "getDir");
    if(!$dir || !isset($dir)){
      die("Error (code:json-002) Config-error.");
    }
    if(!is_dir($dir)){
      mkdir($dir , 0777 , true);
    }
  }



  
  


  




  

  public static function getTableLists($dbname=""){
    $config = \mynt::exec("\\mynt\\lib\\config" , "getData" , array());
    $dir    = \mynt::exec("\\lib\\data\\database" , "getDir");
    $dbname = ($dbname) ? $dbname : $config["database"];
    $dbpath = $dir . $dbname ."/";
    $lists  = array();
    $dirs   = scandir($dbpath); 
    for($i=0; $i<count($dirs); $i++){
      if($dirs[$i] === "." || $dirs[$i] === ".."){continue;}
      if(is_file($dbpath . $dirs[$i])
      && preg_match("/(.+?)\.json$/" , $dirs[$i] , $match)){
        array_push($lists , $match[1]);
      }
      else if(is_dir($dbpath . $dirs[$i])){
        array_push($lists , $dirs[$i]);
      }
      
    }
    if(count($lists)){
      return array("status"=>"ok" , "data" => $lists);
    }
    else{
      return array("status"=>"error" , "message"=>"対象ディレクトリがありません。" , "data"=>array());
    }
  }

  public static function create_table($dbname="",$table=""){
    $config = \mynt::execution("\\mynt\\lib\\config" , "getData" , array());
    $dir    = \mynt::exec("\\lib\\data\\database" , "getDir");
    $dbname = ($dbname) ? $dbname : $config["database"];
    $dbpath = $dir . $dbname ."/";
    if(!is_dir($dbpath)){
      mkdir($dbpath , 0777 , true);
    }

    if(!$table || !isset($config["tables"][$table])){
      return array("status"=>"error","message"=>"テーブルの指定がありません。" , "code"=>"json");
    }

    // indexチェック
    $index_dir  = (isset($config["tables"][$table]["info"]["index_dir"]))  ? (isset($config["tables"][$table]["info"]["index_dir"]))  : "";
    $index_file = (isset($config["tables"][$table]["info"]["index_file"])) ? (isset($config["tables"][$table]["info"]["index_file"])) : "";

    // index-count
    $index_datas = array();
    foreach($config["tables"][$table]["columns"] as $column_name => $column_data){
      if(!isset($column_data["index"])){continue;}
      $index_datas[$column_data["index"]] = true;
    }

    // index無し
    if($index_dir === "" && $index_file === ""){
      $path = $dbpath . $table . ".json";
      if(!is_file($path)){
        file_put_contents($path , "");
        return array("status"=>"ok","index_count"=>count(array_keys($index_datas)));
      }
      else{
        return array("status"=>"error","message"=>"テーブルファイル作成済み。" , "code"=>"json");
      }
    }

    // index有り
    else{
      $path = $dbpath . $table;
      if(!is_dir($path)){
        mkdir($path , 0777 , true);
        return array("status"=>"ok","index_count"=>count(array_keys($index_datas)));
      }
      else{
        return array("status"=>"error","message"=>"テーブルフォルダ作成済み。" , "code"=>"json");
      }
    }


  }

  // $cols = array("%key1"=>"SORT_ASC(降順)" , "%key2"=>"SORT_DESC(昇順)")
  // 結果が連想配列になるので、配列に変換し直さなければならない
  public static function array_msort($array, $cols){
    $colarr = array();
    foreach ($cols as $col => $order) {
      $colarr[$col] = array();
      if($array){
        foreach ($array as $k => $row) {
          $val = isset($row[$col]) ? $row[$col] : "";
          $colarr[$col]['_'.$k] = strtolower($val);
        }
      }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
      $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
      foreach ($arr as $k => $v) {
        $k = substr($k,1);
        if (!isset($ret[$k])) $ret[$k] = $array[$k];
        $val = isset($array[$k]) && isset($array[$k][$col]) ? $array[$k][$col] : "";
        $ret[$k][$col] = $val;
      }
    }
    // return $ret;
    return array_values($ret);
  }

  // 型,デフォルト値の設定
  public static function setStrings($table_name="" , $datas=array()){
    if(!$table_name || !$datas){return;}
    $table_setting = \mynt::exec('\lib\data\database' , "getTableSetting" , array($table_name));
    if(!isset($table_setting["columns"]) || !$table_setting["columns"]){return;}
    $newData = array();
    foreach($table_setting["columns"] as $key => $setting){
      if($key === "id" && !isset($datas[$key])){continue;}

      $val = isset($datas[$key]) ? $datas[$key] : "";
      if(isset($setting["type"])){
        switch($setting["type"]){
          case "INT":
            if($val === "" || $val === null){
              $val = null;
            }
            else{
              $val = (int)$val;
            }
            $val = (isset($setting["default"]) && $val === null) ? (int)\mynt::exec('','',array($table_name , $key)) : $val;
            break;
          case "FLOAT":
            if($val === "" || $val === null){
              $val = null;
            }
            else{
              $val = (float)$val;
            }
            $val = (isset($setting["default"]) && $val === null) ? (float)\mynt::exec('','',array($table_name , $key)) : $val;
            break;
          default:
            $val = (string)$val;
            $val = (isset($setting["default"]) && $val === "") ? \mynt::exec('\lib\data\common','getTableColumns_defaultString',array($table_name , $key)) : $val;
            break;
        }
      }
      $newData[$key] =  $val;
    }

    // 論理削除機能対応
    if(isset($datas["flg"])){
      $newData["flg"] = (int)$datas["flg"];
    }
    return $newData;
  }

  // indexデータ一式を返す。
  // return @ {index : 構造 , datas : data一式 , key_array : key値一式}
  public static function get_index_values($dbname="",$tableName="",$wheres=array()){
    if(!$tableName){return;}
    $dbname   = $dbname ? $dbname : $GLOBALS["page"]["page"];
    $keys     = \mynt::exec('\lib\data\common' , "getIndexKeys" , array($tableName));
    if(!$keys){return;}
    $basePath = "data/".$dbname."/".$tableName."/";
    $vals      = self::get_index_datas($basePath);

    $datas = self::set_index_datas($keys , $vals);
    $keys_array = self::set_index_array_keys($keys);
    $res = array(
      "index" => $keys,
      "datas" => $datas,
      "key_array" => $keys_array
    );
    return json_encode($res , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  public static function get_index_datas($path=""){
    if(!$path || !is_dir($path)){return;}
    $lists = \mynt::exec('\lib\common\dir','lists',array($path , "" , 1 , ""));
    $arr = array();
    foreach($lists as $list){
      if(is_file($path.$list)){
        if(preg_match("/\.json$/" , $list)){
          $val = preg_replace("/\.json$/" , "" , $list);
          array_push($arr , $val);
        }
      }
      else if(is_dir($path.$list)){
        $val = preg_replace("/\/$/" , "" , $list);
        $res = self::get_index_datas($path . $val."/");
        $arr[$val] = $res;
      }
    }
    return $arr;
  }


  public static function set_index_array_keys($keys=null){
    $arr = array();
    foreach($keys as $key => $tables){
      $arr = array_merge($arr , $tables);
    }
    return $arr;
  }
  public static function set_index_datas($keys=null , $vals=null){
    if(!$keys || !$vals){return;}
    $datas = array();

    $num = 0;
    $keys_key = array_keys($keys);
    $keys_val = array_values($keys);
    $key = $keys_key[0];
    $val = $keys_val[0];
    $index = $key;

    unset($keys[$key]);
    foreach($vals as $vals_key => $vals_val){
      if(gettype($vals_val) === "string"){
        array_push($datas , array(
          "index" => $index,
          "key" => $val,
          "val" => explode("_",$vals_val)
        ));
      }
      else if(gettype($vals_val) === "array"){
        array_push($datas , array(
          "index" => $index,
          "key"  => $val,
          "val"  => explode("_",$vals_key),
          "data" => self::set_index_datas($keys , $vals_val)
        ));
      }
    }

    return $datas;
  }

}