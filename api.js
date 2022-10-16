/**
 * Nodejs : api (scraping)共通システム
 * 
 * [How-to]
 * mynt/node/%mode%/index.node.js 
 * $ node node.js --module %api%
 * 
 * [target-programs]
 * api/%module%/api.js
 * 
 */

;(function(){

  process.env.NODE_PATH = __dirname + '/lib/node';
  require('module')._initPaths();

  var argv      = require('argv.node');
  var pathExist = require('path_exist.node');

  // require-api
  var path = typeof argv.options.module !== "undefined" ? __dirname + "/api/" + argv.options.module + "/api.js" : "";
  if(!path){
    console.log("No module.");
  }
  
  else if(!pathExist(path)){
    console.log("No path. ("+path+")");
  }

  // module-run
  else{
    var res = require(path);
    if(res){
      console.log(res);
    }
  }
  
})();