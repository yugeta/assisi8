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

	var $$construct = function(){
    switch(document.readyState){
      case "complete"    : new $$;break;
      case "interactive" : $$event(window , "DOMContentLoaded" , function(){new $$});break;
      default            : $$event(window , "load" , function(){new $$});break;
		}
  };

  var $$ = function(){
    
    // addAccount
    var addAccount = document.querySelector("button.addAccount");
    if(addAccount){
			$$event(addAccount , "click" , (function(e){$$.prototype.clickAdd(e)}).bind(this));
    }

    // lists
    var lists = document.querySelectorAll("table.userLists tbody tr");
    for(var i=0; i<lists.length; i++){
			$$event(lists[i] , "click" , (function(e){$$.prototype.clickList(e)}).bind(this));
    }
  };

  $$.prototype.clickList = function(e){
		var target = e.currentTarget;
		if(!target){
			alert("Error no-target.");
			return;
		}
		var id = target.getAttribute("data-id");
		if(!id){
			alert("Error no-id.");
			return;
		}
    var urlInfo = $$urlinfo(location.href);
    if(id){
      location.href = urlInfo.url +"?p="+ urlInfo.query.p +"&c=users/edit"+"&id="+id;
    }
	};
	
	$$.prototype.clickAdd = function(e){
		var urlInfo = $$urlinfo(location.href);
		location.href = urlInfo.url + "?p="+ urlInfo.query.p +"&c=users/add";
	};

  // var $$urlinfo=function(uri){
	// 	if(!uri){uri = location.href;}
	// 	var data={};
	// 	//URLとクエリ分離分解;
	// 	var query=[];
	// 	if(uri.indexOf("?")!=-1){query = uri.split("?")}
	// 	else if(uri.indexOf(";")!=-1){query = uri.split(";")}
	// 	else{
	// 		query[0] = uri;
	// 		query[1] = '';
	// 	}
	// 	//基本情報取得;
	// 	var sp = query[0].split("/");
	// 	var data={
	// 		url:query[0],
	// 		dir:$$pathinfo(uri).dirname,
	// 		domain:sp[2],
	// 		protocol:sp[0].replace(":",""),
	// 		query:(query[1])?(function(q){
	// 			var data=[];
	// 			var sp = q.split("&");
	// 			for(var i=0;i<sp .length;i++){
	// 				var kv = sp[i].split("=");
	// 				if(!kv[0]){continue}
	// 				data[kv[0]]=kv[1];
	// 			}
	// 			return data;
	// 		})(query[1]):[]
	// 	};
	// 	return data;
	// };
	// var $$pathinfo = function(p){
	// 	var basename="",
	// 	    dirname=[],
	// 			filename=[],
	// 			ext="";
	// 	var p2 = p.split("?");
	// 	var urls = p2[0].split("/");
	// 	for(var i=0; i<urls.length-1; i++){
	// 		dirname.push(urls[i]);
	// 	}
	// 	basename = urls[urls.length-1];
	// 	var basenames = basename.split(".");
	// 	for(var i=0;i<basenames.length-1;i++){
	// 		filename.push(basenames[i]);
	// 	}
	// 	ext = basenames[basenames.length-1];
	// 	return {
	// 		"hostname":urls[2],
	// 		"basename":basename,
	// 		"dirname":dirname.join("/"),
	// 		"filename":filename.join("."),
	// 		"extension":ext,
	// 		"query":(p2[1])?p2[1]:"",
	// 		"path":p2[0]
	// 	};
	// };

	$$construct();
})();
