var fs = require('fs');

module.exports = (function(){
  var MAIN =  function(path){
    if(path.indexOf("/") === -1){
      return {
        dir : "",
        file : path
      };
    }
    
    // multi-path
		var paths = path.split("/");
    var file  = paths.pop();
    var fileInfo = file.split(".");
    var extension = fileInfo.length > 1 ? fileInfo.pop() : "";
    return {
      dir       : paths.join("/"),
      file      : file,
      filename  : fileInfo.join("."),
      extension : extension
    };
  };
  return MAIN
})();