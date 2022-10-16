<?php
namespace lib\menu;

class lists{
  
  public static function view($type="",$tag="li",$tag_base="ul",$tag_option=""){
    if(!$type){return;}
    $res = \mynt::exec('\lib\menu\data' , 'load' , array($type));
    if($res["status"] === "error"){return;}

    $newlists = array();
    // labeling
    foreach($res["data"] as $data){
      if(!isset($data["id"]) || !$data["id"]){continue;}
      $newlists[$data["id"]] = $data;
    }

    // lists-directory
    foreach($newlists as $key => $data){
      if(!isset($data["parent_id"]) || !$data["parent_id"]){continue;}
      if(!isset($newlists[$data["parent_id"]]["lists"])){
        $newlists[$data["parent_id"]]["lists"] = array();
      }
      $newlists[$data["parent_id"]]["lists"][] = $data;
      unset($newlists[$key]);
    }

    return self::html($newlists , $tag , $tag_base , $tag_option , $type);
  }
  public static function view_file($type="",$file=""){
    if(!$type){return;}
    if(!$file || !is_file($file)){return;}

    $txt   = file_get_contents($file);
    $datas = json_decode($txt , true);
    if(!$datas || !count($datas)){return;}

    $newdata = array();
    foreach($datas as $data){
      if($type === $data["type"]){
        $newdata[] = $data;
      }
    }

    return self::html($newdata , "li");
  }

  public static function html($datas=array(),$tag="li",$tag_base="ul",$tag_option="" , $type=""){
    if(!$datas){return;}
    $html = "";

    $datas = self::sort_lists($datas);
    
    foreach($datas as $num => $data){
      // auth
      if(isset($data["auth"]) && $data["auth"] && !self::check_auth($data["auth"])){continue;}

      $key = "";
      if(isset($data["key"]) && $data["key"]){
        $key = ' data-key="'.$data["key"].'"';
      }

      $line = "";

      $style    = isset($data["style"]) ? " style='".$data["style"]."'" : "";
      $attr     = isset($data["attr"])  ? " ".$data["attr"] : "";
      $type_val = $type ? " data-type='".$type."'" : "";
      // if($tag){$line .= "<".$tag.$type_val.$key.$style." ". $tag_option .">";}
      if($tag){$line .= "<".$tag .$type_val.$key.$style." ". $tag_option . $attr .">";}
      
      if(isset($data["link"]) && $data["link"]){
        $line .= "<a href='".$data["link"]."'>";
        $line .= isset($data["html"]) ? $data["html"] : "";
        $line .= "</a>";
      }
      else{
        $line .= isset($data["html"]) ? $data["html"] : "";
      }
      
      if(isset($data["lists"]) && count($data["lists"])){
        // $root = !$line ? "data-type='menu-root'" : "--";
        $line .= "<".$tag_base.">".PHP_EOL;
        $line .= self::html($data["lists"] , $tag , $tag_base , $tag_option);
        $line .= "</".$tag_base.">".PHP_EOL;
      }

      if($tag){$line .= "</".$tag.">";}

      preg_match_all("/{{(.+?)}}/",$line,$matches);
      if($matches && $matches[1]){
        for($i=0; $i<count($matches[1]); $i++){
          if(!isset($data[$matches[1][$i]])){continue;}
          $line = str_replace("{{".$matches[1][$i]."}}" , $data[$matches[1][$i]] , $line);
        }
      }
      
      $html .= $line.PHP_EOL;
      
    }
    $html = \mynt::exec('\lib\html\replace','conv',array($html));
    return $html;
  }

  public static function check_auth($auth){
    if($auth === "null" || !$auth){
      return false;
    }

    // login pattern
    else if($auth === "login" && isset($_SESSION["id"]) && $_SESSION["id"]){
      return true;
    }

    // logout pattern
    else if($auth === "logout" && (!isset($_SESSION["id"]) || !$_SESSION["id"])){
      return true;
    }

    // auth-id pattern
    else if($auth !== "login" && $auth !== "logout" && isset($_SESSION["auth"])){
      $auths = explode("," , $auth);
// print_r($auths);exit();
// echo gettype($auths[0]);exit;
      if(in_array($_SESSION["auth"] , $auths)){
        return true;
      }
      // for($i=0; $i<count($auths); $i++){
      //   if($auths[$i] == $_SESSION["auth"]){
      //     return true;
      //   }
      // }
    }
    
    return false;
  }

  public static function sort_lists($datas){
    $keys = array();
    foreach($datas as $value){
      if(isset($value["sort"]) && $value["sort"] !== ""){
        array_push($keys , $value["sort"]);
      }
      else{
        array_push($keys , 1);
      }
    }
    array_multisort($keys , SORT_ASC , $datas);
    return $datas;
  }

}