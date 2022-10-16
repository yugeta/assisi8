/**
* 指定のパスのディレクトリを作成する（ネストが複数あっても自動作成できる）
*/

var fs        = require('fs');
var pathexist = require("./path_exist.node.js");
// var pathexist = require("./lib/nodejs/path_exist.node.js");

module.exports = (function(){

  var $$ = function(path){
    if(!path){return;}
    var sp = path.split("/");
    var p = "";
    for(var i=0; i<sp.length; i++){
      if(!sp[i]){continue;}
      p += sp[i] + "/";
      if(pathexist(p) === false){
        fs.mkdirSync(p);
      }
      // else{//console.log("exists : "+p);
      //   // return "exists : "+p;
      //   // break;
      // }
    }
    return "success";
  };

  return $$;
})();


// var fs_mkdir = function(path){
//   if(!path){return;}
//   var sp = path.split("/");
//   var p = "";
//   for(var i=0; i<sp.length; i++){
//     if(!sp[i]){continue;}
//     p += sp[i] + "/";
//     if(pathexist(p) === false){
//       if(argv.options.message){
//         console.log("{'message' : '+ "+ p+"'}");
//       }
//       fs.mkdirSync(p);
//     }
//   }
// };
