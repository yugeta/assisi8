;(function(){
  var LIB  = function(){};
  LIB.prototype.event = function(target, mode, func , flg){
    flg = (flg) ? flg : false;
		if (target.addEventListener){target.addEventListener(mode, func, flg)}
		else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
  };
  LIB.prototype.construct = function(){
    switch(document.readyState){
      case "complete"    : new MAIN();break;
      case "interactive" : this.event(window , "DOMContentLoaded" , (function(){new MAIN()}).bind(this));break;
      default            : this.event(window , "load"             , (function(){new MAIN()}).bind(this));break;
		}
  };

  var MAIN = function(){
    var type_select = document.querySelector("[name='database[type]']");
    if(type_select){
      new LIB().event(type_select , "change" , (function(e){this.changeType(e)}).bind(this));
    }
  };

  MAIN.prototype.changeType = function(e){
    var select = e.target;
    if(!select){return;}

    var areas = document.querySelectorAll(".post-data .type-area");
    for(var i=0; i<areas.length; i++){
      if(areas[i].getAttribute("data-type") === select.value){
        areas[i].style.setProperty("display","block","");
      }
      else{
        areas[i].style.setProperty("display","none","");
      }
    }
    // if(select.value == "mysql"){
      
    // }
    // else if(select.value == "net"){

    // }
    // else{

    // }
  };


  new LIB().construct();
})();