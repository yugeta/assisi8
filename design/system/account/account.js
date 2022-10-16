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
    // var btn_login = document.querySelector(".login");
    // if(btn_login){
    //   new LIB().event(btn_login , "click" , (function(e){this.login(e)}).bind(this));
    // }
  };

  MAIN.prototype.login = function(e){
    var mail = document.querySelector("input[name='mail']");
    var pass = document.querySelector("input[name='pass']");
    if(!mail || !pass){return}
    if(!mail.value || !pass.value){
      alert("未入力の項目があります。");
      return;
    }
    var urlinfo = new LIB().urlinfo();

    new $$ajax({
      url : urlinfo.url,
      query : {
        php         : '\\lib\\auth\\login::login_check("'+mail.value+'","'+pass.value+'")',
        exit        : true,
        redirect_ok : "./",
        redirect_ng : "?f=login"
      },
      onSuccess : function(res){
        if(res == "1"){
          location.href = this.query.redirect_ok;
        }
        else{
          var urlinfo = new LIB().urlinfo();
          location.href = urlinfo.uri;
        }
      }
    });
  };


  new LIB().construct();
})();