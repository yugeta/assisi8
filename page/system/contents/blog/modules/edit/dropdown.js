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
    var dropdown_button = document.querySelectorAll("button.dropdown-toggle");
    if(dropdown_button){
      for(var i=0; i<dropdown_button.length; i++){
        new LIB().event(dropdown_button[i] , "click" , (function(e){this.dropdown_toggle(e)}).bind(this));
      }
      
    }
  };

  MAIN.prototype.dropdown_toggle = function(e){
    var button = e.currentTarget;
    var menu_label = button.getAttribute("dropdown-target");
    if(!menu_label){return;}
    var target = document.querySelector(".dropdown-menu[menu-label='"+menu_label+"']");
    if(!target){return;}
    // open (data-toggle=true)
    if(this.dropdown_check(target) !== true){
      this.dropdown_close_all();
      this.button_close_all();
      this.dropdown_open(target);
      this.button_open(button);
    }
    // close (data-toggle=false)
    else{
      this.dropdown_close(target);
      this.button_close(button);
    }
  };

  /* Menu */
  MAIN.prototype.dropdown_check = function(target){
    if(target.getAttribute("data-view") === "1"){
      return true;
    }
    else{
      return false;
    }
  };
  MAIN.prototype.dropdown_open = function(target){
    target.setAttribute("data-view","1");
  };
  MAIN.prototype.dropdown_close = function(target){
    target.removeAttribute("data-view");
  };
  MAIN.prototype.dropdown_close_all = function(){
    var targets = document.querySelectorAll(".dropdown-menu");
    for(var i=0; i<targets.length; i++){
      this.dropdown_close(targets[i]);
    }
  };

  /* Button */
  MAIN.prototype.button_check = function(target){
    if(target.getAttribute("data-active") === "1"){
      return true;
    }
    else{
      return false;
    }
  };
  MAIN.prototype.button_open = function(button){
    button.setAttribute("data-active","1");
  };
  MAIN.prototype.button_close = function(button){
    button.removeAttribute("data-active");
  };
  MAIN.prototype.button_close_all = function(){
    var buttons = document.querySelectorAll(".dropdown-toggle");
    for(var i=0; i<buttons.length; i++){
      this.button_close(buttons[i]);
    }
  };
  


  new LIB().construct();
})();