<?php
namespace page\system\contents\network;

/**
 * - format
 *  [ ip:port , flg(true | false) , memo ]
 */


class proxy{
  public static $path = "data/network/proxy.csv";
  public static $temp = "page/system/contents/network/proxy_template.html";
  public static function lists(){
    if(!is_file(self::$path)){return;}
    $lists = explode("\n" , file_get_contents(self::$path));
    $temp = file_get_contents(self::$temp);

    $html = "";
    for($i=0; $i<count($lists); $i++){
      if(!$lists[$i]){continue;}
      $sp = explode(",",$lists[$i]);
      $tmp = $temp;
      $tmp = str_replace("{{ip-port}}",$sp[0],$tmp);
      $tmp = str_replace("{{flg}}",$sp[1],$tmp);
      $tmp = str_replace("{{memo}}",join(",",array_slice($sp,2)),$tmp);
      $html .= $tmp;
    }
    return $html;
  }

  public static function access($ip_port=""){
    // if(!$ip_port){return;}
    $ua    = "proxy-checker";
    stream_context_set_default(
      array(
        'http' => array(
          'ignore_errors'   => true,
          'method'          => 'GET',
          'header'          =>  
            'Connection: close'."\r\n".
            'Accept-language: ja'."\r\n".
            "User-Agent: ".$ua."\r\n".
            'Proxy-Connection: close'."\r\n",
          'proxy'           => 'tcp://'.$ip_port,
          'request_fulluri' => true,
          'timeout'         => 5
        )
      )
    );
    $url = "http://myntinc.com/proxy_check.php";
    $data = file_get_contents($url);
    return $data;
  }
}