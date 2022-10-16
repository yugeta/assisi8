;(function(){
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

  LIB.prototype.construct = function(){
    switch(document.readyState){
      case "complete"    : new MAIN();break;
      case "interactive" : this.event(window , "DOMContentLoaded" , function(){new MAIN()});break;
      default            : this.event(window , "load"             , function(){new MAIN()});break;
    }
  };

  var MAIN = function(){
    var elm_select = document.getElementById("change_type");
    if(elm_select){
      new LIB().event(elm_select , "change" , (function(e){this.changeSelect(e)}).bind(this))
    }
  };

  MAIN.prototype.changeSelect = function(e){
    var urlinfo = new LIB().urlinfo();
    var target = e.target;
    var url = urlinfo.url +"?p="+ urlinfo.query.p +"&c="+ urlinfo.query.c +"&type=" + target.value;
    location.href = url;
  };

  

  new LIB().construct();
})();