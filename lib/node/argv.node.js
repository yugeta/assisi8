/**
* getopt : argv情報を連想配列で返す
*/
module.exports = (function(){
  var argv = {argv:[] , options:{}};
  var $$ = function(){
    if(process.argv.length < 2){return {};}

    for(var i=2; i<process.argv.length; i++){
      var data = this.getString(process.argv[i]);
      switch(data.type){
        case "short":
          var res1 = this.getString(process.argv[i]);
          var next = (typeof process.argv[i+1] === "undefined") ? "" : process.argv[i+1];
          var res2 = this.getString(next);
          if(res2.type === "string"){
            this.setOptions(res1.string , res2.string);
            i++;
          }
          else{
            this.setOptions(res1.string , true);
          }
          break;

        case "short-multi":
          var res = this.getString(process.argv[i]);
          for(var j=0; j<res.string.length; j++){
            var key = res.string.charAt(j);
            if(key === ""){continue;}
            this.setOptions(key , true);
          }
          break;

        case "long":
        // console.log(process.argv[i]);
          var res1 = this.getString(process.argv[i]);
          var next = (typeof process.argv[i+1] === "undefined") ? "" : process.argv[i+1];
          var res2 = this.getString(next);
          if(res2.type === "string"){
            this.setOptions(res1.string , res2.string);
            i++;
          }
          else{
            this.setArgv(process.argv[i]);
          }
          break;

        case "keyvalue":
          // argv[data.key] = data.value;
          this.setOptions(data.key , data.value);
          break;

        case "string":
        // console.log(process.argv[i]);
          this.setArgv(data.string);
          break;
      }
    }
  };

  $$.prototype.getString = function(str){
    if(str === "" || str === "-" || str === "--"){
      return {type:"" , string:""};
    }
    else if(str.match(/^\-\-(.+?)$/)){
      return {type:"long" , string:RegExp.$1};
    }
    // short-single
    else if(str.match(/^\-([0-9a-zA-Z])$/)){
      return {type:"short" , string:RegExp.$1};
    }
    // short-multi
    else if(str.match(/^\-([0-9a-zA-Z]+?)$/)){
      return {type:"short-multi" , string:RegExp.$1};
    }
    // key-value
    else if(str.match(/^(.+?)=(.+?)$/)){
      return {type:"keyvalue" , key:RegExp.$1 , value:RegExp.$2};
    }
    else{
      return {type:"string" , string:str};
    }
  };
  $$.prototype.setOptions = function(key , value){
    if(typeof argv.options[key] === "undefined"){
      argv.options[key] = value;
    }
    else{
      if(typeof argv.options[key] === "string"){argv.options[key] =[argv.options[key]];}
      argv.options[key].push(value);
    }
  };
  $$.prototype.setArgv = function(value){
    if(value){
      argv.argv.push(value);
    }
  }

  new $$;
  return argv;
})();
