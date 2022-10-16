var fs        = require('fs');
var pathexist = require("./path_exist.node.js");

module.exports = (function(){

  return function(path){
    if(!path){
      return {
        status : "error",
        message : "not designation path."
      };
    }

    if(pathexist(path) === false){
      return {
        status : "error",
        message : "not exists. ("+path+")"
      };
    }

    // file
    if(!fs.statSync(path).isDirectory()){
      fs.unlinkSync(path);
      return {
        status : "ok",
        message : "file removed. ("+path+")"
      };
    }

    // remove-all
    var counts = rmPaths(path);

    // return
    return {
      status : "ok",
      message : "directory removed.",
      count : counts
    };
  };

  function rmPaths(path){
    var counts = 0;

    // directory
    if(checkPathType(path) === "dir"){
      dir = path.match(/\/$/) ? path : path+"/";
      var paths = getUnders(path);
      for(var i=0; i<paths.length; i++){
        counts += rmPaths(dir + paths[i]);
      }
      fs.rmdirSync(path);
    }

    // remove : file or dir
    else{
      fs.unlinkSync(path);
    }

    // return
    counts++;
    return counts;
  }

  function getUnders(path){
    return fs.readdirSync(path);
  };

  function checkPathType(path){
    return fs.statSync(path).isDirectory() ? "dir" : "file;"
  };
})();
