<?php
namespace lib\blog\php;

class data{

  public static $table_name_blog    = "blog/data";
  public static $table_name_article = "blog/article";

  // load-all
  public static $cache_all = array();
  public static function load_all($type=1 , $status=0){
    $type = $type ? (int)$type : 1;
    if(!isset(self::$cache_all[$type])){
      $where = array(
        "type" => $type
      );
      if($status){
        $where["status"] = $status;
      }
      $sort = array(
        "schedule" => "SORT_DESC"
      );
      self::$cache_all[$type] = \mynt::data_load($GLOBALS["page"]["page"] , self::$table_name_blog , array() , $where , $sort);
    }
    return self::$cache_all[$type];
  }

  public static $cache_lists = array();
  public static function load_lists_all($type=1 , $group_id=null , $tag="" , $still_today_flg=false , $status=null){
    $res   = self::load_all($type , $status);
    if($res["status"] === "ok"){
      $today = $still_today_flg ? (int)date("YmdHis") : null;
      // 対象記事一覧の選別 [schedule , group , tag]
      $all_lists = array();
      foreach($res["data"] as $data){
        if($status && $status !== $data["status"]){continue;}
        // status===none(All表示)の時は削除は非表示にする。
        if(!$status && $data["status"] === 5){continue;}
        if($today && $today < (int)$data["schedule"]){continue;}
        if($group_id && $data["group"] && $group_id != $data["group"]){continue;}
        if($tag && !\mynt::exec('\lib\blog\php\tag','check_inner_tag',array($tag , $data["tag"]))){continue;}
        // if($tag && !\mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\tag','check_inner_tag',array($tag , $data["tag"]))){continue;}
        array_push($all_lists , $data);
      }
      $res["data"] = $all_lists;
    }
    return $res;
  }

  // load-lists
  public static function load_lists($type=1 , $count=10 , $current_num=0 , $status=0 , $group_id=null , $tag="" , $search="" , $still_today_flg=false){
    $type        = (int)$type;
    // $count       = (int)$count;
    // $current_num = (int)$current_num;
    $status      = (int)$status;
    $res = self::load_lists_all($type , $group_id , $tag , $still_today_flg , $status);
// return $res;
    $total_count = 0;
    $num = 0;
    if(count($res["data"])){
      // $total_count = count($res["data"]);
      $group_datas  = \mynt::exec('\lib\blog\php\group' ,'load_datas',array($type));
      $status_datas = \mynt::exec('\lib\blog\php\status','load_datas');
      // $group_datas  = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\group' ,'load_datas',array($type));
      // $status_datas = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\status','load_datas');
      $st_num = $current_num ? (int)$current_num * (int)$count : 0;
      $res["st_num"] = $st_num;
      // $res["data"] = array_slice($res["data"] , $st_num , $count);
      $datas = [];
      foreach($res["data"] as $data){
        $num++;
        // if($status && $status !== $data["status"]){continue;}
        // $data = $all_lists[$i];
        $res_article = self::load_article($data["type"] , $data["id"]);
        if($res_article["status"] === "ok"){
          $data = array_merge($data , $res_article["data"][0]);
        }

        // search検索
        if($search !== ""){
          if(isset($data["title"])
          && isset($data["article"])
          && strstr($data["title"] , $search) === false
          && strstr($data["article"] , $search) === false){
            continue;
          }
        }
        $total_count++;
        
        // 表示上限、下限数オーバーチェック
        if(count($datas) >= $count){continue;}
        else if($num <= $st_num){continue;}

        $group_name = isset($group_datas[$data["group"]]["name"]) ? $group_datas[$data["group"]]["name"] : "";
        $tag        = isset($data["tag"]) ? $data["tag"] : "";
        $status_name = isset($status_datas[$data["status"]]["name"]) ? $status_datas[$data["status"]]["name"] : "";
        if($status_name === "公開"
        && (int)$data["schedule"] > (int)date("YmdHis")){
          $status_name = "予定";
        }

        $data["ymd"]                   = \mynt::exec('\lib\string\date','ymd2format'   ,array($data["schedule"]));
        $data["ymdhis"]                = \mynt::exec('\lib\string\date','ymdhis2format',array($data["schedule"]));
        $data["tag_values"]            = self::set_tag_arr2vals($data["tag"]);
        $data["group_name"]            = $group_name;
        $data["group_name&tag_values"] = $group_name ."/". self::set_tag_arr2vals($data["tag"]);
        $data["status_name"]           = $status_name;
        array_push($datas , $data);
        // if(count($datas) >= $count){
        //   break;
        // }
        // 
      }
      $res["data"]        = $datas;
      $res["total_count"] = $total_count;
    }
    
    

    // // $current_num = $current_num ? $current_num + 1 : 1;
    // $start_num = $current_num ? $current_num * $count : 0;
    // $end_num   = $current_num ? ($current_num+1) * $count : $count;
    // $data_num  = 0;
    // $datas = [];
    // foreach($res["data"] as $data){
    //   if($data_num >= $end_num){break;}
    //   if($data_num < $start_num){
    //     $data_num++;
    //     continue;
    //   }
    //   if($today && $today < (int)$data["schedule"]){continue;}
    //   if($group_id && $data["group"] && $group_id != $data["group"]){continue;}
    //   if($tag && !\mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\tag','check_inner_tag',array($tag , $data["tag"]))){continue;}
    //   $res_article = self::load_article($data["type"] , $data["id"]);
    //   if($res_article["status"] === "ok"){
    //     $data = array_merge($data , $res_article["data"][0]);
    //   }
    //   $data["ymd"]    = \mynt::exec('\lib\string\date','ymd2format'   ,array($data["schedule"]));
    //   $data["ymdhis"] = \mynt::exec('\lib\string\date','ymdhis2format',array($data["schedule"]));
    //   $data["tag_values"] = self::set_tag_arr2vals($data["tag"]);
    //   $data["group_name"] = $group_datas[$data["group"]]["name"];
    //   $data["group_name&tag_values"] = $group_datas[$data["group"]]["name"] ."/". self::set_tag_arr2vals($data["tag"]);
    //   $data["status_name"] = $status_datas[$data["status"]]["name"];
    //   array_push($datas , $data);
    //   $data_num++;
    //   // if($count && count($datas) >= $count){break;}
    // }

    
    return $res;
  }
  public static function load_lists_json($type=1 , $count=10 , $current_num=0 , $status=0 , $group_id=null , $tag="" , $search="" , $today_flg=true){
    if(!$type){return;}
    $status = $status ? (int)$status : 0;
    $res = self::load_lists($type , $count , $current_num , $status , $group_id , $tag , $search , $today_flg);
    return json_encode($res ,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }
  public static function load_lists_json_data($type=1 , $count=10 , $current_num=0 , $status=0 , $group_id=null , $tag="" , $search="" , $today_flg=false){
    if(!$type){return;}
    $status = $status ? (int)$status : 0;
    $res = self::load_lists($type , $count , $current_num , $status , $group_id , $tag , $search , $today_flg);
    if($res["status"] !== "ok"){return;}
    return json_encode($res["data"] ,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  // load-article
  public static $data = array();
  public static function load_data($type=1 , $id=null){
    if(!$type || !$id){return;}
    $type = $type ? $type : 1;
    if(!isset(self::$data[$type])){self::$data[$type] = array();}
    if(!isset(self::$data[$type][$id])){
      $where = array(
        "type" => $type,
        "id"   => $id
      );
      $res = \mynt::data_load($GLOBALS["page"]["page"] , self::$table_name_blog , array() , $where);
      if($res["status"] === "ok"){
        $res_article = self::load_article($type , $id);
        if($res_article["status"] === "ok"){
          // $res["article"] = $res_article["data"];
          $res["data"][0] = array_merge($res["data"][0] , $res_article["data"][0]);
        }
      }
      self::$data[$type][$id] = $res;
    }
    return self::$data[$type][$id];
  }
  public static function load_data_json($type=1 , $id=null){
    if(!$type || !$id){return;}
    $res = self::load_data($type , $id);
    return json_encode($res ,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  // load-article
  public static function load_article($type=1 , $id=null){
    if(!$type || !$id){return;}
    $where = array(
      "type" => $type,
      "id"   => $id
    );
    return \mynt::data_load($GLOBALS["page"]["page"] , self::$table_name_article , array() , $where);
  }
  public static function load_article_json($type=1 , $id=null){
    if(!$type || !$id){return;}
    $res = self::load_article($type , $id);
    return json_encode($res ,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }


  // save

  public static function save(){
    $update = date("YmdHis");
    $type   = isset($_POST["type"]) && $_POST["type"] ? $_POST["type"] : 1;

    // group新規登録
    if($_POST["group"] === "" && $_POST["group_name"] !== ""){
      // $res = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\group','save',array($type , $_POST["group_name"]));
      $res = \mynt::exec('\lib\blog\php\group','save',array($type , $_POST["group_name"]));
      if($res["status"] === "ok"){
        $_POST["group"] = $res["data"]["id"];
      }
    }

    // list-data
    $data_blog = array(
      "type"     => $type,
      "article"  => $_POST["article"],
      "group"    => $_POST["group"],
      "tag"      => $_POST["tag"],
      "schedule" => $_POST["schedule"],
      "status"   => $_POST["status"],
      "uid"      => $_POST["uid"],
      "entry"    => $update
    );
    if(isset($_POST["id"]) && $_POST["id"]){
      $data_blog["id"] = $_POST["id"];
    }
    $res_blog = \mynt::data_save($GLOBALS["page"]["page"] , self::$table_name_blog , $data_blog);

    if($res_blog["status"] !== "ok"){
      return $res_blog;
    }

    // article
    $res_article = self::save_article(
      $type,
      $res_blog["data"]["id"],
      $_POST["article"],
      $_POST["title"],
      $_POST["eyecatch"],
      $update
    );
    // $data_article = array(
    //   "type"     => $type,
    //   "id"       => $res_blog["data"]["id"],
    //   "article"  => $_POST["article"],
    //   "title"    => $_POST["title"],
    //   "eyecatch" => $_POST["eyecatch"],
    //   "entry"    => $update
    // );
    // $res_article = \mynt::data_save($GLOBALS["page"]["page"] , self::$table_name_article , $data_article);

    if($res_article["status"] === "ok"){
      $res_blog["article"] = $res_article["data"];
    }

    return $res_blog;
  }
  public static function save_article($type , $id , $article , $title , $eyecatch , $update=""){

    //check-exist
    $where = array(
      "type"=>$type,
      "id"=>$id
    );
    $check_exist = \mynt::data_load($GLOBALS["page"]["page"] , self::$table_name_article , array() , $where);
    if($check_exist["status"] === "ok"){
    
    // 改行文字列調整
    $article = str_replace("\r\n","\n",$article);
    $article = str_replace("\r","",$article);

      if($article === $check_exist["data"][0]["article"]
      && $eyecatch === $check_exist["data"][0]["eyecatch"]
      && $title === $check_exist["data"][0]["title"]){
        return $check_exist;
      }
    }

    $update = $update ? $update : date("YmdHis");
    $data_article = array(
      "type"     => $type,
      "id"       => $id,
      "article"  => $article,
      "title"    => $title,
      "eyecatch" => $eyecatch,
      "entry"    => $update
    );
    return \mynt::data_save($GLOBALS["page"]["page"] , self::$table_name_article , $data_article);
  }

  public static function save_post(){
    $res = self::save();
    if(!$res || $res["status"] !== "ok"){return;}

    // return json_encode($res ,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    // $uri = \mynt::exec('\lib\url\common','getUri');
    $url = \mynt::exec('\lib\url\common','getUrl');
    $uri = $url . "?c=blog/edit&type=". $res["data"]["type"] ."&id=". $res["data"]["id"];
    \mynt::exec('\lib\url\common' , 'setUrl' , array($uri));
  }


  public static function get_value($type=1 , $id=null , $key="" , $flg=false){
    if(!$id || !$key){return;}
    $type = $type ? $type : 1;
    $res = self::load_data($type , $id);
    if($res["status"] !== "ok"){return;}
    $val = isset($res["data"][0][$key]) ? $res["data"][0][$key] : "";
    if($flg === "escape"){
      $val = htmlspecialchars($val);
    }
    return $val;
  }

  public static function get_article_string($type=1 , $id=null){
    if(!$type || !$id){return;}
    $res = self::load_data($type , $id);
    if($res["status"] !== "ok"){return;}
    if(!isset($res["data"][0]["article"])){return;}
    $article = $res["data"][0]["article"];
    $article = str_replace("\n" , "" , $article);
    $article = str_replace("\r" , "" , $article);
    $article = str_replace("\t" , "" , $article);
    // $article = str_replace("<","",$article);
    // $article = str_replace(">","",$article);
    $article = self::except_tag($article);
    $article = htmlspecialchars($article);
    // $article = str_replace('"' , "&quot;" , $article);
    return $article;
  }

  public static function except_tag($str=""){
    if(!$str){return $str;}
    $str = preg_replace("/<.+?>/" , "" , $str);
    return $str;
  }

  public static function data_count($type=1 , $status=0 , $group="" , $tag="" , $search=""){
    $status = $status ? (int)$status : 0;
    // $res = self::load_all($type,$status);
// return $type ."/". $group ."/". $tag ."/". $status;

    $res = self::load_lists_all($type , $group , $tag , false , $status);
// return json_encode($res);
    if($res["status"] === "ok"){
      return count($res["data"]);
    }
    else{
      return 0;
    }
  }

  public static function set_tag_arr2vals($tag_str=""){
    if(!$tag_str){return "";}
    $arr = json_decode($tag_str , true);
    for($i=0; $i<count($arr); $i++){
      $arr[$i] = "#".$arr[$i];
    }
    return join(",",$arr);
  }

  // public static function datetime2ymdhis($str){
  //   if(!$str){return;}
  //   $sp = explode("T",$str);
  //   $sp_date = explode("-",$sp[0]);
  //   $sp_time = explode(":",$sp[1]);
  //   $y = $sp_date[0];
  //   $m = sprintf("%02d",$sp_date[1]);
  //   $d = sprintf("%02d",$sp_date[2]);
  //   $h = sprintf("%02d",$sp_time[0]);
  //   $i = sprintf("%02d",$sp_time[1]);
  //   $s = "00";
  //   return $y.$m.$d.$h.$i.$s;
  // }
}