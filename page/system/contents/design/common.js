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
      case "interactive" : this.event(window , "DOMContentLoaded" , (function(){new MAIN()}).bind(this));break;
      default            : this.event(window , "load"             , (function(){new MAIN()}).bind(this));break;
		}
  };

  var MAIN = function(){
    var btn_active = document.querySelectorAll("a.btn-active");
    if(btn_active && btn_active.length){
      for(var i=0; i<btn_active.length; i++){
        new LIB().event(btn_active[i] , "click" , (function(e){this.active(e)}).bind(this));
      }
    }
  };

  MAIN.prototype.active = function(e){
    var target = e.currentTarget;
    if(!target){return;}
    var target_id = target.getAttribute("data-id");
    if(!target_id){return;}

    // データ置換
    var urlinfo = new LIB().urlinfo();
    new $$ajax({
      url : urlinfo.url,
      query : {
        php : "\\page\\system\\contents\\design\\common::changeDesign()",
        exit : true,
        design_id : target_id
      },
      onSuccess : function(res){
console.log(res);
        // 切り替え成功の場合、active表示を切り替える
        if(res == 1){
          var target_id = this.query.design_id;
          var btn_active = document.querySelectorAll("a.btn-active");
          for(var i=0; i<btn_active.length; i++){
            var li = btn_active[i].parentNode;
            var current_id = btn_active[i].getAttribute("data-id");
            if(current_id === target_id){
              li.setAttribute("data-active","1");
            }
            else{
              li.setAttribute("data-active","0");
            }
          }
        }
        else{
          console.log("Error . no save.");
        }
      }
    });
  };

  


  new LIB().construct();
})();