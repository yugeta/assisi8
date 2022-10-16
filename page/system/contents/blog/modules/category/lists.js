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
  LIB.prototype.construct = function(){
    switch(document.readyState){
      case "complete"    : new MAIN();break;
      case "interactive" : this.event(window , "DOMContentLoaded" , (function(){new MAIN()}).bind(this));break;
      default            : this.event(window , "load"             , (function(){new MAIN()}).bind(this));break;
		}
  };

  var MAIN = function(){
    var lists = document.querySelectorAll(".lists table tbody tr");
    if(lists && lists.length){
      for(var i=0; i<lists.length; i++){
        new LIB().event(lists[i] , "click" , (function(e){this.clickList(e)}).bind(this));
      }
    }

    var add_button = document.querySelector(".add-area button.add");
    if(add_button){
      new LIB().event(add_button , "click" , (function(e){this.clickAdd(e)}).bind(this));
    }
  };

  MAIN.prototype.clickList = function(e){
    var target = e.currentTarget;
    var id = target.getAttribute("data-id");
    if(!id){return;}

    var urlinfo = new LIB().urlinfo();
    url = urlinfo.url +"?"+ "p=system" +"&c=blog/category_edit&id=" + id;
    location.href = url;
  };
  MAIN.prototype.clickAdd = function(e){
    var urlinfo = new LIB().urlinfo();
    url = urlinfo.url +"?"+ "p=system" +"&c=blog/category_edit";
    location.href = url;
  };

  


  new LIB().construct();
})();