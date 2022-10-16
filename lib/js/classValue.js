;$classValue = (function(){

  // mode @ [check , add , del , toggle]
  var $$ = function(mode , element , value){
    switch(mode){
      case "check":
        return this.check(element , value);
        break;
        
      case "add":
        return this.add(element , value);
        break;
        
      case "del":
        return this.del(element , value);
        break;
        
      case "toggle":
        return this.toggle(element , value);
        break;
    }
    return false;
  };
  
  // 任意エレメントのclass名の検索
  // return @ [true : exist , false : none]
  $$.prototype.check = function(element , value){
    if(!element || !value){return false;}
    var className = element.getAttribute("class");
    if(!className){
      return false;
    }
    var classNames = className.split(" ");
    if(classNames.indexOf(value) !== -1){
      return true;
    }
    else{
      return false;
    }
  };
  
  // 任意のclass名を追加する
  // return @ [true : success , false : fail]
  $$.prototype.add = function(element , value){
    if(!element || !value){return false;}
    if(!this.check(element , value)){
      element.className += " " + value;
      return true;
    }
    else{
      return false;
    }
  };
  
  // 任意のclass名を除外する
  // return @ [true : success , false : fail]
  $$.prototype.del = function(element , value){
    if(!element || !value){return false;}
    var classNames = this.check(element , value);
    if(!classNames){
      return false;
    }
    else{
      var newClass = [];
      for(var i=0; i<classNames.length; i++){
        if(classNames[i] === value){continue;}
        newClass.push(classNames[i]);
      }
      element.className = newClass.join(" ");
      return true;
    }
  };
  
  // 任意のclass名を付けたり取ったりする
  // return @ ["del" : exist->none , "add" : none->exist]
  $$.prototype.toggle = function(element , value){
    if(!element || !value){return false;}
    if(this.check(element , value)){
      this.del(element , value);
      return "del";
    }
    else{
      this.add(element , value);
      return "add";
    }
  };
  
  return $$;
})();

