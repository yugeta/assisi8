;(function(){
  // ページ内にjsライブラリの読み込み
  var $$addScript = function(file){
    var s = document.createElement("script");
    s.src = file;
    document.body.appendChild(s);
  }

  // イベントライブラリ
  var $$event = function(target, mode, func){
		//other Browser
		if (typeof target.addEventListener !== "undefined"){
      target.addEventListener(mode, func, false);
    }
    else if(typeof target.attachEvent !== "undefined"){
      target.attachEvent('on' + mode, function(){func.call(target , window.event)});
    }
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
		,	url      : (sp.length) ? sp.join("/") : ""
    , dir      : (sp.length) ? sp.slice(0 , sp.length-1).join("/") +"/" : ""
    , file     : (sp.length) ? sp.pop() : ""
		,	domain   : (sp.length) ? sp[2] : ""
    , protocol : (sp.length) ? sp[0].replace(":","") : ""
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


  var $$init = function(){
    var state = document.readyState;
		if(state === "complete"){
			new $$
		}
		else if(state === "interactive"){
			$$event(window , "DOMContentLoaded" , function(){new $$});
		}
		else{
			$$event(window , "load" , function(){new $$});
		}
  };

   var $$ = function(){

    // dropdown-set
    this.set_dropdown(".header-menu");
    this.set_dropdown(".side-menu");

    // data-type
    this.set_dataTypeLinks();

    // mobile-list-button
    var mobileListButton = document.querySelector(".menu .mobile-list-button");
    if(mobileListButton){
      $$event(mobileListButton , "click" , (function(e){this.click_mobileListButton(e)}).bind(this));
    }

    // hashtag-set
    var urlinfo  = $$urlinfo(location.href);
    // var links = document.links;
    var links = document.querySelectorAll(".menu li > a");
    for(var i=0; i<links.length; i++){

      var href = links[i].getAttribute("href");
      if(href.match(/^javascript:/)){continue;}
      // if(!href.match(/^#(.+?)$/)){continue;}
      // links[i].setAttribute("data-filename" , RegExp.$1);
      // $$event(links[i] , "click" , (function(e){
      //   var filename = e.currentTarget.getAttribute("data-filename");
      //   this.clickHadhtag(filename , ".contents")}
      // ).bind(this));

      // active-set
      var li = links[i].parentNode;
      if(li.tagName !== "LI"){continue;}
      var hrefinfo = $$urlinfo(href);
      if(hrefinfo.query.p
      && urlinfo.query.p
      && hrefinfo.query.p === urlinfo.query.p){
        li.className = "active";
      }
      else if(!urlinfo.query.p
      && li.getAttribute("data-default")){
        li.className = "active";
      }

    }
  };

  $$.prototype.set_dropdown = function(root_selector){
    if(!root_selector){return;}
    var menus = document.querySelectorAll(root_selector + " ul > li");
    for(var i=0; i<menus.length; i++){
      var listsElm = menus[i].querySelectorAll(":scope > ul");
      if(!listsElm.length){continue;}
      menus[i].setAttribute("data-dropdown" , "1");
      var a = menus[i].querySelector(":scope > a");
      a.href = "javascript:void(0)";
      $$event(a , "click" , (function(e){this.set_menu_toggle(e)}).bind(this));
    }
  };

  $$.prototype.set_dataTypeLinks = function(){
    var header_menu_lists = document.querySelectorAll(".header-menu li > a");
    for(var i=0; i<header_menu_lists.length; i++){
      // dropdownは対象外
      if(header_menu_lists[i].parentNode.getAttribute("data-bropdown") === "1"){continue;}
      // data-typeがない場合は対象外
      var dataLink = header_menu_lists[i].getAttribute("data-link");
      if(!dataLink){continue;}
      header_menu_lists[i].href = "javascript:void(0)";
      $$event(header_menu_lists[i] , "click" , (function(e){
        var target = e.currentTarget;
        var dataLink = target.getAttribute("data-link");
        if(!dataLink){return;}
        if(!dataLink){return;}
        var side_menu_lists   = document.querySelectorAll(".side-menu > ul");
        for(var i=0; i<side_menu_lists.length; i++){
          var sideDataLink = side_menu_lists[i].getAttribute("data-link");
          if(!sideDataLink){continue;}
          if(sideDataLink === dataLink){
            side_menu_lists[i].setAttribute("data-view" , "active");
          }
          else{
            side_menu_lists[i].removeAttribute("data-view");
          }
        }
      }).bind(this));
    }
  };

  $$.prototype.set_menu_toggle = function(e){
    var currentTarget = e.currentTarget.parentNode;

    // toggle
    if(currentTarget.getAttribute("data-view") !== "1"){
      currentTarget.setAttribute("data-view","1");
      this.set_all_menu_remove(currentTarget);
    }
    else if(currentTarget.getAttribute("data-view") === "1"){
      currentTarget.setAttribute("data-view","0");
    }
  };

  // all-link-hidden-quick
  $$.prototype.set_all_menu_remove = function(exclusion_target){
    // 上位エレメントを取得
    var parents = this.getParents(exclusion_target.parentNode , ".menu" , "*[data-dropdown='1']");

    var listElm = document.querySelectorAll(".menu > ul > li");
    for(var i=0; i<listElm.length; i++){
      if(listElm[i].getAttribute("data-dropdown") !== "1"){continue;}
      if(listElm[i].getAttribute("data-dropdown") === null){continue;}
      // 対象エレメントは閉じない
      if(exclusion_target && exclusion_target === listElm[i]){continue;}
      // 対象エレメントの親要素は閉じない
      if(parents.indexOf(listElm[i]) !== -1){continue;}
      // 閉じる処理
      listElm[i].removeAttribute("data-view");
    }
  };

  $$.prototype.click_mobileListButton = function(e){
    var menus = document.querySelectorAll(".menu");
    if(!menus.length){return;}

    var checkValue = menus[0].getAttribute("data-view");
    for(var i=0; i<menus.length; i++){
      if(checkValue === "1"){
        menus[i].setAttribute("data-view" , "0");
      }
      else{
        menus[i].setAttribute("data-view" , "1");
      }
    }
  };

  // 任意エレメントから上位の指定エレメントまでで任意selectorのモノをピックアップして配列で返す。
  // currentElement @ 任意エレメント
  // rootSelector   @ 最上位エレメントのselector（無ければdocument.body)
  // targetSelector @ 対象の任意selector（無ければ上位全て）
  $$.prototype.getParents = function(currentElement , rootSelector , targetSelector){
    rootSelector = (rootSelector) ? rootSelector   : "body";

    var parents = [];
    while(!currentElement.matches(rootSelector)){
      if(targetSelector && !currentElement.matches(targetSelector)){
        currentElement = currentElement.parentNode;
        continue;
      }
      parents.push(currentElement);
      currentElement = currentElement.parentNode;
    }
    return parents;
  };

  // ハッシュタグリンクをクリックした時
  $$.prototype.clickHadhtag = function(filename , selector){
    var loadTime_start = (+new Date());
    var ajax = new $$MYNT_AJAX;
    ajax.loadHTML("html/"+filename+".html" , selector);
    var loadTime_finish = (+new Date());
    var loadTime = document.getElementById("loadTime");
    if(loadTime !== null){
      loadTime.textContent = "Loading-time : "+ ((loadTime_finish - loadTime_start) / 1000) +" s";
    }
  };

  new $$init;
})();