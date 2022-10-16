<?php
namespace page\system\contents\blog;

class test_convBlogData{
  public static function file2jsons(){
    $dir = "data/blog/";
    // $lists = array_diff(scandir($old_dir) , array(".",".."));
    unset($lists);
    exec("ls ".$dir."*.json" , $lists);

    $setting = \mynt::exec('\lib\data\database','getSetting');
    $save_path = "data/".$setting["database"]."/lib_blog.json";
    echo "<pre>".PHP_EOL;
    echo $save_path.PHP_EOL;

    for($i=0; $i<count($lists); $i++){
      $path = $lists[$i];
      $json = json_decode(file_get_contents($path),true);
      // \mynt::data_save("","lib_blog",$json);
      // $json["file"] = "data/blog/".$json["id"];
      // unset($json["id"]);
      // unset($json["discription"]);
      // unset($json["source"]);
      unset($json2);
      $json2 = array(
        "file"     => (string)$json["id"],
        // "category" => "a",
        "group_id" => $json["group"],
        "tag"      => $json["tag"],
        "type"     => $json["type"],
        "status"   => $json["status"],
        "title"    => $json["title"],
        "image"    => ($json["eyecatch"] && $json["eyecatch_ext"]) ? $json["eyecatch"].".".$json["eyecatch_ext"] : "",
        "date"     => date("YmdHis",$json["schedule"]),
        "entry"    => date("YmdHis",$json["update"])
      );
      // $wheres = array(
      //   "category" => "0",
      // );
      // $txt = json_encode($json2 , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      // file_put_contents($save_path , $txt."\n" , FILE_APPEND);
      $res = \mynt::data_save("" , "lib_blog" , $json2);
      echo $i." : ". $path .PHP_EOL;
    }
    echo "</pre>".PHP_EOL;
  }
}