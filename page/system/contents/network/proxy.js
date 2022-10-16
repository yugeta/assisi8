(function(){

  var __options = {};

  var MAIN = function(){
    this.loaded_run();
    this.set_add_button();
    this.set_list_edit();
    this.set_control();
  };

  MAIN.prototype.loaded_run = function(){
    this.lists = document.querySelectorAll(".lists table tbody tr[data-flg='true'][data-check=''] .ip-port");
    if(!this.lists || !this.lists.length){return;}
    this.access_proxy(this.lists[0].textContent);
  };

  MAIN.prototype.access_proxy = function(ip_port){
    this.start_time = (+new Date());
    new $$ajax({
      url : location.href,
      query : {
        php  : '\\page\\system\\contents\\network\\proxy::access("'+ ip_port +'")',
        exit : true
      },
      onSuccess : (function(ip_port , res){
        var sp = ip_port.split(":");
        var check_elm = this.lists[0].parentNode.querySelector(".check");
        var check = sp[0] === res ? true : false;
        var str = check ? "○" : "×";
        var tim = parseInt(((+new Date()) - this.start_time) / 100 , 10) /10;
        check_elm.textContent = str +" "+ tim+"s";
        check_elm.parentNode.setAttribute("data-check" , check);
        this.loaded_run();
      }).bind(this , ip_port)
    });
  };

  MAIN.prototype.set_list_edit = function(){
    var lists = document.querySelectorAll(".lists table tbody tr");
    for(var i=0; i<lists.length; i++){
      new LIB().event(lists[i] , "click" , (function(e){this.click_lists(e)}).bind(this));
    }
  };

  MAIN.prototype.click_lists = function(e){
    // console.log(e);
    this.modal_window();
  };

  MAIN.prototype.set_add_button = function(){
    var add_button = document.querySelectorAll(".add-area button.add");
    for(var i=0; i<add_button.length; i++){
      new LIB().event(add_button[i] , "click" , (function(e){this.click_add_button(e)}).bind(this));
    }
  };

  MAIN.prototype.click_add_button = function(e){
    // console.log(e);
    this.modal_window();
  };

  MAIN.prototype.modal_window = function(){
    var html = "";

    var buttons = [];

    this.modal_view = new $$modal({
      // 表示サイズ
      size    : {
        width : "500px",
        height: "auto"
      },
      // 表示位置
      position : {
        vertical : "center",
        horizon  : "center",
        margin   : ["10px","10px","10px","10px"]
      },
      // 閉じるボタン
      close   : {
        html  : "",
        size  : 20,
        click : function(){}
      },
      // [上段] タイトル表示文字列
      title   : "Proxyを登録",
      // [中断] メッセージ表示スタイル
      message : {
        html   : html,
        height : "auto",
        align  : "center"
      },
      // [下段] ボタン
      button  : buttons,
      // クリック挙動 [ "close" , "none" ]
      bgClick : "none",
      loaded : (function(e){}).bind(this)
    });
  };

  MAIN.prototype.set_control = function(){
    var list_view_mode = document.querySelectorAll(".list-view-mode");
    for(var i=0; i<list_view_mode.length; i++){
      new LIB().event(list_view_mode[i] , "click" , (function(e){this.click_list_view_mode(e)}).bind(this));
    }
  };

  MAIN.prototype.click_list_view_mode = function(e){
    var table = document.querySelector(".lists table");
    if(table.getAttribute("data-view-mode") === "short"){
      table.setAttribute("data-view-mode" , "all");
    }
    else{
      table.setAttribute("data-view-mode" , "short");
    }
  };



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
  

  new LIB().construct();
})();