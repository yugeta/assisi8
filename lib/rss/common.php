<?php
namespace lib\rss;

class common{
  public static function xml($header=array() , $datas=array()){
    $RSS = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <language>ja</language>
  </channel>
</rss>
EOT;

    $url = \mynt::exec('\lib\url\common','getUrl');
    
    $XML = new \SimpleXMLElement($RSS);
    $XML->channel->addChild('title'       , $header["title"]);
    $XML->channel->addChild('link'        , $url . $header["filename"]);
    $XML->channel->addChild('description' , $header["description"]);

    foreach($datas as $data){
      $item = $XML->channel->addChild('item');
      foreach($data as $key => $value){
        $value = htmlspecialchars($value , ENT_XML1 , "UTF-8");
        $item->addChild($key       , $value);
      }
    }
    
    return $XML->asXML();
  }
}