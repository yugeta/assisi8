;(function(){

	var $$event = function(target, mode, func){
		if (target.addEventListener){target.addEventListener(mode, func, false)}
		else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
	};

	var $$urlinfo = function(uri){
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

	var __construct = function(){
    switch(document.readyState){
      case "complete"    : new $$;break;
      case "interactive" : $$event(window , "DOMContentLoaded" , function(){new $$});break;
      default            : $$event(window , "load" , function(){new $$});break;
		}
  };

  var $$ = function(){
    
    // cancel
    var btn_cancel = document.querySelector("button.btn-cancel");
    if(btn_cancel){
			$$event(btn_cancel , "click" , (function(e){$$.prototype.btn_cancel(e)}).bind(this));
    }

    // remove
    var btn_remove = document.querySelector("button.btn-remove");
    if(btn_remove){
			$$event(btn_remove , "click" , (function(e){$$.prototype.btn_remove(e)}).bind(this));
    }

  };

  $$.prototype.btn_cancel = function(e){
		location.href='?p=system&c=users/index';
	};
	
	$$.prototype.btn_remove = function(e){
    if(!confirm("このアカウントを削除してもよろしいですか？この操作は取り消せません。")){return;}
		document.forms.form_remove.submit();
	};

	__construct();
})();
