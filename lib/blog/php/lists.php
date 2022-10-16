<?php
namespace lib\blog\php;

class lists{
  public static function get_all_datas(){
    $res = \mynt::data_load($GLOBALS["page"]["page"],'blog/data');
    if($res["status"] === "ok"){
      return $res["data"];
    }
    else{
      return;
    }
  }
  
  public static function view_lists_li($type=1 , $count=10 , $current_num=0 , $group_id=null , $tag="" , $search=""){
    $type = $type ? $type : 1;
    $release_status = \mynt::exec('\lib\blog\php\status','get_release_id');
    // $release_status = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\status','get_release_id');
    $still_today_flg = true;
    $status = "";
    $res = \mynt::exec('\lib\blog\php\data','load_lists',array($type , $count , $current_num , $release_status , $group_id , $tag , $search , $still_today_flg));
    // $res = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','load_lists',array($type , $count , $current_num , $release_status , $group_id , $tag , $still_today_flg , $status));
    if(!$res || $res["status"] !== "ok"){return;}
    $template = self::get_template();
    $html = "";
    preg_match_all("/{{(.+?)}}/",$template,$matches);
    $group_datas = \mynt::exec('\lib\blog\php\group','load_datas',array($type));
    // $group_datas = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\group','load_datas',array($type));
    foreach($res["data"] as $num => $data){
      $tmp = $template;
      for($i=0; $i<count($matches[1]); $i++){
        $key = $matches[1][$i];
        if($key === "date"){
          $value = \mynt::exec('\lib\string\date','ymd2format',array($data["schedule"]));
        }
        else if($key === "group-name"){
          // $value = $group_datas[$data["group"]]["name"];
          $group = isset($data["group"]) ? $data["group"] : "";
          $value = $group && isset($group_datas[$group]) && isset($group_datas[$group]["name"]) ? $group_datas[$group]["name"] : "";
        }
        else if(isset($data[$key])){
          $value = $data[$key];
        }
        else{
          $value = "";
        }
        $tmp = str_replace("{{".$key."}}" , $value , $tmp);
      }
      $html .= $tmp;
    }
    return $html;
  }

  public static function get_template(){
    $path = "lib/blog/html/lists_li.html";
    // $path = "page/".$GLOBALS["page"]["page"]."/contents/blog/html/lists_li.html";
    $template = "";
    if(is_file($path)){
      $template = file_get_contents($path);
      $template = \mynt::exec('\lib\html\replace','conv',array($template));
    }
    if(!$template){
      $template = "<li>{{title}}</li>";
    }
    return $template;
  }
  public static function data_count($type=1,$group_id=null , $tag="" , $still_today_flg=false){
    $status = \mynt::exec('\lib\blog\php\status','get_release_id');
    // $status = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\status','get_release_id');
    // $res    = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','load_all',array($type,$status));
    // $res    = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','load_lists_all',array($type , $group_id , $tag , $still_today_flg));
    $res    = \mynt::exec('\lib\blog\php\data','load_lists_all',array($type , $group_id , $tag , $still_today_flg));
    if($res["status"] === "ok"){
      return count($res["data"]);
    }
    else{
      return 0;
    }
  }

}