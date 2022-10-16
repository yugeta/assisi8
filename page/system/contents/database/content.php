<?php
namespace page\system\contents\database;

class content{
  public static function getSettingValue($key=""){
    if(!$key){return;}
    $setting_database = \mynt::exec('\lib\data\database',"getSetting");
    if($setting_database && isset($setting_database[$key])){
      return $setting_database[$key];
    }
    else{
      return;
    }
  }

  public static function viewTables_tr(){
    $table_lists = \mynt::exec('\lib\data\table','getLists');
// return $table_lists;
    if(!$table_lists || !count($table_lists)){return;}

    // $table_exist_lists = \mynt::exec("\\mynt\\lib\\data","getTableLists",array());
    // $tables = $table_exist_lists["data"];
    $tables = array();

    $html = "";
    $num  = 1;
    foreach($table_lists as $table_name){
      $table_name = str_replace(".json","",$table_name);
      $flg = ($tables) ? array_search($table_name , $tables) : false;
      $table_setting = \mynt::exec('\lib\data\database','getSetting',array($table_name));

      if(isset($table_setting["info"])
      && isset($table_setting["info"]["mode"])
      && $table_setting["info"]["mode"] === "system"){
        $html .= "<tr class='system-table-exist'>";
        $html .= "<th>".$num."</th>";
        $html .= "<td><a href='?p=database_table&table_name=".$table_name."'>".$table_name."</a></td>";
        $html .= "<td>";
        $html .= ($flg !== false) ? "Exists" : "Not-found";
        $html .= "</td>";
        $html .= "<td></td>";
        $html .= "<td>System-setting</td>";
        $html .= "</tr>";
        $num++;
        array_splice($tables , $flg , 1);
        continue;
      }
      else if($flg !== false){
        array_splice($tables , $flg , 1);
      }

      $clasName = ($flg !== false) ? "" : "not-found";
      $html .= "<tr class='".$clasName."'>";
      $html .= "<th>".$num."</th>";

      // name
      $html .= "<td><a href='?p=database_table&table_name=".$table_name."'>".$table_name."</a></td>";

      // exist
      $html .= "<td>";
      $html .= ($flg !== false) ? "Exists" : "Not-found";
      $html .= "</td>";

      // index
      $indexes = array();
      if(isset($table_setting["columns"])){
        foreach($table_setting["columns"] as $column_name => $column_data){
          if(isset($column_data["index"]) && $column_data["index"]){
            if(!isset($indexes[$column_data["index"]])){
              $indexes[$column_data["index"]] = array();
            }
            array_push($indexes[$column_data["index"]] , $column_name);
          }
        }
      }
      
      $html .= "<td>";
      $html .= (array_keys($indexes)) ? join(",",array_keys($indexes)) : "";
      $html .= "</td>";

      // button
      $html .= "<td>";
      $html .= ($flg !== false) ? "" : "<button type='submit' name='table_name' value='".$table_name."'>作成</button>";
      $html .= "</td>";
      $html .= "</tr>";
      $num++;
    }
    for($i=0; $i<count($tables); $i++){
      $html .= "<tr class='table-exist'>";
      $html .= "<th>".$num."</th>";
      $html .= "<td><a href='?p=database_table&table_name=".$tables[$i]."'>".$tables[$i]."</a></td>";
      $html .= "<td>Exists</td>";
      $html .= "<td></td>";
      $html .= "<td>Not-setting</td>";
      $html .= "</tr>";
      $num++;
    }

    return $html;
  }

  public static function makeDataArea(){
    $config_data = \mynt::exec("\\mynt\\lib\\config","getData",array());
    if(isset($_REQUEST["table_name"])
    && $_REQUEST["table_name"]
    && isset($config_data["tables"][$_REQUEST["table_name"]])){
      switch($config_data["type"]){
        case "mysql" : 
        $flg = \mynt::exec("\\mynt\\lib\\data_mysql","create_table",array("",$_REQUEST["table_name"]));
        break;
  
        case "net"   : 
        break;
  
        default      : 
        $flg = \mynt::exec("\\mynt\\lib\\data_json","create_table",array("",$_REQUEST["table_name"]));
        break;
      }
    }

    // redirect
    if(isset($_REQUEST["redirect"]) && $_REQUEST["redirect"]){
      \mynt::exec("\\mynt\\lib\\url","setUrl",array($_REQUEST["redirect"]));
    }
    else{
      $url = \mynt::exec("\\mynt\\lib\\url","getUrl",array());
      \mynt::exec("\\mynt\\lib\\url","setUrl",array($url));
    }
    exit("redirected...");
    
  }
}