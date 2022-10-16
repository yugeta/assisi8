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
    var addTags = document.querySelectorAll(".tagControl .addTag");
    if(addTags){
      for(var i=0; i<addTags.length; i++){
        new LIB().event(addTags[i] , "click" , (function(e){this.addTags(e)}).bind(this));
      }
    }
  };

  MAIN.prototype.addTags = function(e){
    var elm = e.currentTarget;
    if(!elm){return;}
console.log(elm);
    switch(elm.getAttribute("data-mode")){
      case "hr":console.log("hr");
        this.setEvent_addTag_proc_single("hr");
        break;
      case "img":
      case "a":
      case "h2":
      case "h3":
      case "h4":
      case "h5":
      case "h6":
      case "p":
      case "blockquote":
      case "strong":
      
      case "table+":
      case "table":
      case "thead":
      case "tbody":
      case "tfoot":
      case "tr":
      case "td":
      case "th":
      case "ul+":
      case "ol+":
      case "dl+":
      case "ul":
      case "ol":
      case "li":
      case "dl":
      case "dt":
      case "dd":
      case "form+":
      case "text":
      case "hidden":
      case "radio":
      case "checkbox":
      case "select":
      case "textarea":
      case "button":
      case "submit":
      case "submcodeit":
      default:
    }
  };

  // MAIN.prototype.addTag_insert = function(tag){
  //   this.setEvent_addTag_proc(tag,"","");
  // };

  MAIN.prototype.setEvent_addTag_proc_single = function(tag){
		if(!tag){return;}
    var source = document.querySelector('textarea[name="source"]');
    if(source){return;}


console.log(source);
		// add-textarea
    var sentence = source.value;//全部文字
    var before   = sentence.substr(0, source.selectionStart);
		var after    = sentence.substr(source.selectionEnd, len);
    var word     = "<"+tag+">";
		sentence = before + word + after;
		source.value = sentence;
	};
  MAIN.prototype.setEvent_addTag_proc = function(tag,str){
		if(!tag){
			alert("tag指定がありません");
			return;
		}
		var source = document.getElementById('textarea[name="source"]');
		// add-textarea
		var sentence = source.value;//全部文字
		var len      = sentence.length;//文字全体のサイズ
		// var pos      = source.selectionStart;//選択している最初の位置
		var before   = sentence.substr(0, source.selectionStart);
		var after    = sentence.substr(source.selectionEnd, len);
		var str2     = sentence.substr(source.selectionStart , (source.selectionEnd - source.selectionStart));
		var str      = str + str2;
    var word     = "<"+tag+">"+str+"</"+tag+">";
		sentence = before + word + after;
		source.value = sentence;
	};


  new LIB().construct();
})();