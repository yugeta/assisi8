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
  LIB.prototype.upperSelector = function(elm , selectors) {
    selectors = (typeof selectors === "object") ? selectors : [selectors];
    if(!elm || !selectors){return;}
    var flg = null;
    for(var i=0; i<selectors.length; i++){
      for (var cur=elm; cur; cur=cur.parentElement) {
        if(!cur.parentElement){
          return;
          break;
        }
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

  var MAIN = function(){

    // user-add
    var user_add = document.querySelector(".user-add");
    if(user_add){
      new LIB().event(user_add , "click" , (function(e){this.clickUserAdd(e)}).bind(this));
    }

    // window-click
    new LIB().event(window , "click" , (function(e){this.clickWindow(e)}).bind(this));

    // users-list
    this.setUsers();

    // delete
    var btn_remove = document.querySelectorAll(".btn-remove");
    if(btn_remove){
      for(var i=0; i<btn_remove.length; i++){
        new LIB().event(btn_remove[i] , "click" , (function(e){this.clickRemove(e)}).bind(this));
      }
    }
  };

  MAIN.prototype.setUsers = function(){
    var urlinfo = new LIB().urlinfo;
    new $$ajax({
      url : urlinfo.url,
      query : {
        php : '\\page\\system\\contents\\blog\\category::getUsers()',
        exit : true
      },
      onSuccess : (function(res){
        if(!res){return;}
        var json = JSON.parse(res);
        if(!json){return;}
        var data = {};
        for(var i=0; i<json.length; i++){
          data[json[i].id] = json[i];
        }

        var input = document.querySelector("input[name='users']");
        if(!input){return;}
        
        var area = document.querySelector(".users .area");
        if(!area){return;}

        var value = input.value;
        if(value){console.log(value);
          var users = value.split(",");
          for(var i=0; i<users.length; i++){
            var id = Number(users[i]);
            var name = typeof data[id].name !== "undefined" ? data[id].name : data[id].mail;
            var span = this.addSpan(id , name);
            area.appendChild(span);
          }
        }
      }).bind(this)
    });
  };

  MAIN.prototype.clickUserAdd = function(e){
    var area = document.querySelector(".users > .area");
    if(!area){return;}
    this.viewUserList();
  };

  MAIN.prototype.viewUserList = function(){
    var urlinfo = new LIB().urlinfo;
    new $$ajax({
      url : urlinfo.url,
      query : {
        php : '\\page\\system\\contents\\blog\\category::getUsers()',
        exit : true
      },
      onSuccess : (function(res){
        if(!res){return;}
        var json = JSON.parse(res);
        if(!json){return;}

        var user_lists = document.querySelector(".user-lists");
        if(user_lists){return;}

        var div = document.createElement("div");
        div.setAttribute("class" , "user-lists");

        for(var i=0; i<json.length; i++){
          if(document.querySelector(".users .area > *[data-id='"+json[i].id+"']")){continue;}
          var name = typeof json[i].name !== "undefined" && json[i].name ? json[i].name : json[i].mail;
          var list = document.createElement("div");
          list.setAttribute("class","user-list");
          list.setAttribute("data-id" , json[i].id);
          list.textContent = name;
          new LIB().event(list , "click" , (function(e){this.clickUserList(e)}).bind(this));
          div.appendChild(list);
        }
        
        var user_area = document.querySelector(".user-area");
        if(!user_area){return;}
        user_area.appendChild(div);
      }).bind(this)
    });
  };

  MAIN.prototype.clickUserList = function(e){
    var area = document.querySelector(".users .area");
    if(!area){return;}

    var target = e.currentTarget;
    var id = target.getAttribute("data-id");
    var span = this.addSpan(id , target.textContent);
    area.appendChild(span);

    var list = document.querySelector(".user-lists .user-list[data-id='"+id+"']");
    if(list){
      list.setAttribute("data-hidden" , "1");
    }

    this.addUser_input();
  };

  MAIN.prototype.addSpan = function(id , name){
    var span = document.createElement("span");
    span.setAttribute("data-id",id);
    span.textContent = name;
    new LIB().event(span , "click" , (function(e){this.clickUserRemove(e)}).bind(this));
    return span;
  };

  MAIN.prototype.clickWindow = function(e){
    var target = e.target;
    if(!target){return;}
    if(new LIB().upperSelector(target , ".user-lists")){return;}
    var user_lists = document.querySelector(".user-lists");
    if(user_lists){
      user_lists.parentNode.removeChild(user_lists);
    }
  };

  MAIN.prototype.clickUserRemove = function(e){
    var target = e.currentTarget;
    if(!target){return;}
    target.parentNode.removeChild(target);

    this.addUser_input();
  };

  MAIN.prototype.addUser_input = function(){
    var input = document.querySelector("input[name='users']");
    if(!input){return;}

    var lists = document.querySelectorAll(".users .area > *");
    if(!lists || !lists.length){
      input.value = "";
      return;
    }

    var users = [];
    for(var i=0; i<lists.length; i++){
      users.push(Number(lists[i].getAttribute("data-id")));
    }
    input.value = users.join(",");
  };

  MAIN.prototype.clickRemove = function(e){
    if(!confirm("このカテゴリーを削除してもよろしいですか？")){return;}
    var target = e.currentTarget;
    var id = target.getAttribute("data-id");
    if(!id){
      console.log("削除ボタンにdata-idがセットされていません。");
      return;
    }
    var urlinfo = new LIB().urlinfo;
    new $$ajax({
      url : urlinfo.url,
      query : {
        php : '\\page\\system\\contents\\blog\\category::remove("'+id+'")',
        exit : true
      },
      onSuccess : (function(res){console.log(res);
        var redirect = document.forms.form1.redirect.value;
        location.href = redirect;
      }).bind(this)
    });
  };
  


  new LIB().construct();
})();