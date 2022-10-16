(function(){
  var LIB  = function(){};
  LIB.prototype.event = function(target, mode, func , flg){
    flg = (flg) ? flg : false;
		if (target.addEventListener){target.addEventListener(mode, func, flg)}
		else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
  };
  LIB.prototype.urlinfo = function(uri){
    uri = (uri) ? uri : location.href;
    var data={};
		//URLとクエリ分離分解;
    var urls_hash  = uri.split("#");
    var urls_query = urls_hash[0].split("?");
		//基本情報取得;
		var sp   = urls_query[0].split("/");
		var data = {
      uri      : uri
		,	url      : sp.join("/")
    , dir      : sp.slice(0 , sp.length-1).join("/") +"/"
    , file     : sp.pop()
		,	domain   : sp[2]
    , protocol : sp[0].replace(":","")
    , hash     : (urls_hash[1]) ? urls_hash[1] : ""
		,	query    : (urls_query[1])?(function(urls_query){
				var data = {};
				var sp   = urls_query.split("#")[0].split("&");
				for(var i=0;i<sp .length;i++){
					var kv = sp[i].split("=");
					if(!kv[0]){continue}
					data[kv[0]]=kv[1];
				}
				return data;
			})(urls_query[1]):[]
		};
		return data;
  };
  LIB.prototype.upperSelector = function(elm , selectors) {
    selectors = (typeof selectors === "object") ? selectors : [selectors];
    if(!elm || !selectors){return;}
    var flg = null;
    for(var i=0; i<selectors.length; i++){
      for (var cur=elm; cur; cur=cur.parentElement) {
        if (cur.matches(selectors[i])) {
          flg = true;
          break;
        }
      }
      if(flg){
        break;
      }
    }
    return cur;
  }
  LIB.prototype.construct = function(){
    switch(document.readyState){
      case "complete"    : new MAIN();break;
      case "interactive" : this.event(window , "DOMContentLoaded" , (function(){new MAIN()}).bind(this));break;
      default            : this.event(window , "load"             , (function(){new MAIN()}).bind(this));break;
		}
  };


  var MAIN = function(){
    this.typeChange();

    var type_chenge_elm = document.querySelectorAll(".contents-area .type select");
    if(type_chenge_elm){
      for(var i=0; i<type_chenge_elm.length; i++){
        new LIB().event(type_chenge_elm[i] , "change" , (function(e){
          var target = e.currentTarget;
          var current_type = target.value;
          this.type_narrow(current_type);
        }).bind(this));
      }
    }

  };

  MAIN.prototype.typeChange = function(){
    var type_elm = document.querySelector(".contents-area .type select");
    if(!type_elm){return;}
    var current_type = type_elm.value;
    this.type_narrow(current_type);
  };

  MAIN.prototype.type_narrow = function(type_value){
    var table_rows = document.querySelectorAll(".contents-area .menu-list table tbody tr");
    if(!table_rows){return;}
    for(var i=0; i<table_rows.length; i++){
      var type_list = table_rows[i].getAttribute("data-type");
      if(type_list === type_value){
        table_rows[i].setAttribute("data-hidden","0");
      }
      else{
        table_rows[i].setAttribute("data-hidden","1");
      }
    }
  };




  new LIB().construct();
})();