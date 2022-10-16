<?php
namespace page\system\contents\debug;

class info{
  public static function view(){
    return phpinfo(-1);
  }
}