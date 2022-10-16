<?php
namespace page\system\contents\blog_edit;

class data{
  public static function post($id=""){
// die("post!! " . $_POST["id"]);
    $id     = $_POST["id"];
    $update = date("YmdHis");

    // info
    $data = array(
      "category" => $_POST["category"],
      "uid"      => $_POST["uid"],
      "tag"      => $_POST["tag"],
      "file"     => $_POST["file"],
      "status"   => $_POST["status"],
      "title"    => $_POST["title"],
      "image"    => $_POST["eyecatch"],
      "date"     => self::datetime2ymdhis($_POST["date"]),
      "entry"    => $update
    );
    if($id){$data["id"] = $id;}
// print_r($data);exit();
    $res = \mynt::data_save('','lib_blog',$data);
    if($res["status"] === "error"){
      die("Error !! データ登録に失敗しました。");
    }
// print_r($res);
    // source
    $source_dir = \mynt::exec('\lib\blog\source','getDir',array($_POST["category"]));
    if(!is_dir($source_dir)){
      mkdir($source_dir , 0777 , true);
    }
    $source_path = $source_dir . $res["data"]["id"].".html";
// die($source_path);
// die($source_path);
    file_put_contents($source_path , $_POST["source"]);
    
    if(isset($_POST["redirect"])){
      \mynt::exec('\lib\url\common','setUrl',array($_POST["redirect"]));
    }
    else{
      $url = \mynt::exec('\lib\url\common','getUrl');
      \mynt::exec('\lib\url\common','setUrl',array($url));
    }
  }

  public static function datetime2ymdhis($str){
    if(!$str){return;}
    $sp = explode("T",$str);
    $sp_date = explode("-",$sp[0]);
    $sp_time = explode(":",$sp[1]);
    $y = $sp_date[0];
    $m = sprintf("%02d",$sp_date[1]);
    $d = sprintf("%02d",$sp_date[2]);
    $h = sprintf("%02d",$sp_time[0]);
    $i = sprintf("%02d",$sp_time[1]);
    $s = "00";
    return $y.$m.$d.$h.$i.$s;
  }
}