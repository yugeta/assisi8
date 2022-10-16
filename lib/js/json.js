window.$$JSON = (function(){
  let MAIN = function(){};

  MAIN.prototype.stringify_format = function(data , type){
    this.replace_str = [];
    // this.count = 0;
    this.type = type || "string";
    let str = JSON.stringify(data , this.json_replacer.bind(this) , "  ");
    str = this.replace_json_strings(str , this.replace_str);
    return str;
  };

  MAIN.prototype.json_replacer = function(key , value){
    if(value.constructor === Array
    && this.check_array_types(value)){
      // let str = this.join_data(value);
      this.replace_str.push({
        count : this.count,
        data : value
      });
      // this.count++;
      return "{{json_replace:"+ (this.replace_str.length - 1) +"}}";
    }
    else{
      return value;
    }
  };

  MAIN.prototype.check_array_types = function(value){
    // 複数タイプの指定(or)
    if(this.type.constructor === Array){
      for(let val of value){
        let flg = 0;
        for(let i=0; i<this.type.length; i++){
          if(this.type[i] === null && val === null){
            flg++;
          }
          else if(typeof val === this.type[i]){
            flg++;
          }
        }
        if(flg === 0){
          return false;
        }
      }
    }
    // 単一タイプの指定
    else{
      for(let val of value){
        if(typeof val !== this.type){
          return false;
        }
      }
    }
    return true;
  };

  MAIN.prototype.join_data = function(datas){
    return JSON.stringify(datas);
  };

  MAIN.prototype.replace_json_strings = function(str , datas){
    if(!str || !datas){return;}
    for(let i in datas){
      let key = '"{{json_replace:'+ i +'}}"';
      let val = JSON.stringify(datas[i].data);
      str = str.replace(key , val);
    }
    return str;
  };

  return MAIN;
})()