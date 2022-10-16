var fs = require('fs');

module.exports = (function(){
  var $$ = function(path){
    return fs.readdirSync(path);
  };
  return $$;
});