<?php
namespace lib\auth;

class data{

  public static function save($datas=array()){
    if(!$datas){return;}

    // entry
    $datas["entry"] = date("YmdHis");
// print_r($datas);exit();
    return \mynt::data_save("","lib_account",$datas);

    // save-account
    // $data_account = array(
    //   "auth"  => $auth,
    //   "mail"  => $mail,
    //   "pass"  => $pass,
    //   "entry" => $update
    // );
  }

  public static function save_account($datas=array()){
    if(!$datas){return;}
    $datas = array(
      "id"     => $id,
      "mail"   => $_POST["account"]["mail"],
      "auth"   => $_POST["account"]["auth"],
      // "md5"    => md5($_POST["account"]["password"]),
      "pass"   => \mynt::exec('\lib\auth\password','encode',array($_POST["account"]["pass"])),
      "date"   => $update,
      "entry"  => $update
    );
    $res = \mynt::exec("\\mynt\\lib\\data" , "save" , array("","account",$datas));
    if($res["status"] === "error"){
      die("Error データが正常に追加できません。");
    }
  }
  public static function save_property($datas=array()){
    if(!$datas){return;}
    $dataProperty = array(
      "id"     => $id,
      "name"   => $_POST["property"]["name"],
      "memo"   => $_POST["property"]["memo"],
      "date"   => $update,
      "entry"  => $update
    );
    $res = \mynt::exec("\\mynt\\lib\\data" , "save" , array("","property",$dataProperty));
    if($res["status"] === "error"){
      die("Error データが正常に追加できません。");
    }
  }

}