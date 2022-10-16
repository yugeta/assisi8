/**
* 任意のパス（階層）が存在するか確認する関数
* [how-to]
* var path_exist = require('./lib/nodejs/path_exist.node.js');
* var path = "hoge.txt";
* if(new path_exist(path)){ %OK% } else { %NG% }
*/
var fs = require('fs');

module.exports = (function(){
  var $$ = function(path) {
    try{
      fs.accessSync(path);
      return true;
    }
    catch(err){
      if(err.code === 'ENOENT') return false;
    }
  };
  return $$;
})();
