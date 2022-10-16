
window.$$list = (function(){

  let __options = {
    template : {},
    datas : {},
    loaded : function(datas){},
    modal_viewed : function(e){}
  };

  let MAIN = function(options){
    if(!options){return;}
    this.options = this.set_options(options);
    this.set_modules();
    this.set_template();
    this.set_event();
    this.load_datas();
  };

  // optionsセット
  MAIN.prototype.set_options = function(options){
    var op = JSON.parse(JSON.stringify(__options));
    for(var i in options){
      op[i] = options[i];
    }
    return op;
  };


  MAIN.prototype.set_modules = function(){
    let src     = new LIB().currentScriptTag;
    let urlinfo = new LIB().urlinfo(src);

    // css
    let tag_link  = document.createElement("link");
    tag_link.rel  = "stylesheet";
    tag_link.href = urlinfo.dir + "common.css";
    document.getElementsByTagName("head")[0].appendChild(tag_link);

    // modal
    let tag_modal = document.createElement("script");
    tag_modal.src = "plugin/modal_js/src/modal.js";
    document.getElementsByTagName("head")[0].appendChild(tag_modal);
  };

  MAIN.prototype.set_template = function(){
    var templates = document.querySelectorAll(".template");
    if(!templates){return;}
    for(var i=0; i<templates.length; i++){
      var type = templates[i].getAttribute("data-type");
      if(!type){continue;}
      this.options.template[type] = {
        root_tag : templates[i].tagName,
        html : templates[i].innerHTML
      };
    }
  };

  MAIN.prototype.set_event = function(){
    if(typeof this.options.element.modal_add !== "undefined"){
      var add_button = document.querySelectorAll(this.options.element.modal_add);
      if(add_button){
        for(let i=0; i<add_button.length; i++){
          new $$lib().event(add_button[i] , "click" , (function(e){this.view_modal()}).bind(this));
        }
      }
    }
    
    if(typeof this.options.element.lists_base !== "undefined"){
      var data_area = document.querySelector(this.options.element.lists_base);
      if(data_area){
        new $$lib().event(data_area , "click" , (function(e){this.click_lists(e)}).bind(this));
      }
    }
    
  };

  // MAIN.prototype.click_entry = function(id){
  //   switch(this.options.click_mode){
  //     case "modal":this.view_modal(id);break;
  //     default : this.click_lists(id);
  //   }
  // };
  MAIN.prototype.view_modal = function(id){
    let html = this.click_entry_html(this.options.template_name.modal);
    if(!html){
      alert("Error !!! templateが設定されていません。");
      return;
    }
    if(id){
      html = this.click_entry_value(html , id);
    }

    this.modal = new $$modal({
      // 表示サイズ
      size    : {
        width : this.options.modal.width || "400px",
        height: "auto"
      },
      // 表示位置
      position : {
        vertical : "center",  // 縦 [top , *center(*画像などがある場合はサイズ指定してから使用すること) , bottom]
        horizon  : "center",  // 横 [left , *center , right]
        margin   : ["10px","10px","10px","10px"]   // [上、右、下、左]
      },
      // 閉じるボタン
      close   : {
        html  : "",
        size  : 20,
        click : function(){}
      },
      // [上段] タイトル表示文字列
      title   : this.options.modal.title,
      // [中断] メッセージ表示スタイル
      message : {
        html   : html,
        height : "auto",
        align  : "center"
      },
      // [下段] ボタン
      button  : [
        { // mode:"close",
          text:"削除",
          click : (function(e){this.del(e)}).bind(this)
        },
        { // mode:"close",
          text:"登録",
          click : (function(e){this.save(e)}).bind(this)
        }
      ],
      // クリック挙動 [ "close" , "none" ]
      bgClick : "none",
      loaded : this.options.viewed
    });
  };
  MAIN.prototype.click_entry_html = function(type){
    if(typeof this.options.template[type] === "undefined" || !this.options.template[type]){return;}
    let html = this.options.template[type].html;
    return html;
  };
  MAIN.prototype.click_entry_value = function(html , id){
    if(!html || !id){return;}
    if(typeof this.options.datas[id] === "undefined"){return;}
    let doc = new DOMParser().parseFromString(html , "text/html");
    for(let i in this.options.datas[id]){
      var elm = doc.querySelector(".input [name='"+i+"']");
      if(!elm){continue;}
      switch(elm.tagName){
        case "INPUT":
          elm.setAttribute("value" , this.options.datas[id][i]);
          break;
        case "TEXTAREA":
          elm.textContent = this.options.datas[id][i];
          break;
        case "SELECT":
          for(let j=0; j<elm.options.length; j++){
            if(elm.options[j].value == this.options.datas[id][i]){
              elm.options[j].setAttribute("selected" , "true");
            }
          }
          break;
      }
    }
    return doc.body.innerHTML;
  };

  MAIN.prototype.save = function(){
    // let uid   = document.getElementById("uid").value;
    let page  = this.options.page;
    let table = this.options.table;
    let query = this.get_form_value();
    query.php  = this.options.save_php || '\\lib\\lists\\common::save_json("'+page+'","'+table+'")';
    query.exit = true;
    new $$ajax({
      url   : location.href,
      query : query,
      onSuccess : (function(res){
        if(this.options.debug){
          console.log(res);
        }
        if(res){
          let json = JSON.parse(res);
          this.options.datas[json.id] = json;
          let flg = this.list_change(json);
          if(!flg){
            this.list_append(json);
          }
        }
        this.modal.close();
      }).bind(this)
    });
  };
  MAIN.prototype.get_form_value = function(){
    var modal_contents = document.querySelectorAll(".modal-message-contents input,.modal-message-contents select,.modal-message-contents textarea");
    if(!modal_contents){return;}
    let query  = {data:{}};
    if(this.options.query){
      for(let i in this.options.query){
        query["data["+i+"]"] = this.options.query[i];
      }
    }
    for(let i=0; i<modal_contents.length; i++){
      if(modal_contents[i].name === "id" && !modal_contents[i].value){continue;}
      query["data["+modal_contents[i].name+"]"] = modal_contents[i].value;
    }
    return query;
  };
  MAIN.prototype.del = function(){
    if(!confirm("このデータを削除してもよろしいですか？※この操作は取り消せません。")){return;}
    // let uid   = document.getElementById("uid").value;
    let page  = this.options.page;
    let table = this.options.table;
    let query = this.get_form_value_del();
    query.php  = this.options.del_php || '\\lib\\lists\\common::del_json("'+page+'","'+table+'")';
    query.exit = true;
    new $$ajax({
      url   : location.href,
      query : query,
      onSuccess : (function(res){console.log(res);
        if(this.options.debug){
          console.log(res);
        }
        if(res){
          let target,json = JSON.parse(res);
          for(let i=0; i<json.length; i++){
            target = document.querySelector(this.options.element.lists_base + "> *[data-id='"+json[i].id+"']");
            if(target){
              target.parentNode.removeChild(target);
            }
          }
        }
        this.modal.close();
      }).bind(this)
    });
  };
  MAIN.prototype.get_form_value_del = function(){
    var modal_contents = document.querySelectorAll(".modal-message-contents input,.modal-message-contents select,.modal-message-contents textarea");
    if(!modal_contents){return;}
    let query  = {data:{}};
    if(this.options.query){
      for(let i in this.options.query){
        if(this.options.database_unique_keys.indexOf(i) === -1){continue;}
        query["data["+i+"]"] = this.options.query[i];
      }
    }
    for(let i=0; i<modal_contents.length; i++){
      if(modal_contents[i].name === "id" && !modal_contents[i].value){continue;}
      query["data["+modal_contents[i].name+"]"] = modal_contents[i].value;
    }
    return query;
  };


  MAIN.prototype.load_datas = function(){
    // let uid    = this.options.uid;
    let page   = this.options.page;
    let table  = this.options.table;
    let query  = {data:{}};
    if(this.options.query){
      for(let i in this.options.query){
        query["data["+i+"]"] = this.options.query[i];
      }
    }
    if(this.options.debug){
      console.log(query);
      console.log(page+"/"+table);
    }
    query.php  = this.options.load_php || '\\lib\\lists\\common::load_jsons("'+ page +'","'+ table +'")';
    query.exit = true;
    new $$ajax({
      url   : location.href,
      query : query,
      onSuccess : (function(res){
        if(this.options.debug){
          console.log(res);
        }
        if(!res){return;}
        let json = JSON.parse(res);
        for(let i=0; i<json.length; i++){
          this.list_append(json[i]);
          this.options.datas[json[i].id] = json[i];
        }
        this.check_empty_view();
        if(this.options.loaded){
          this.options.loaded(json);
        }
      }).bind(this)
    });
  };
  MAIN.prototype.list_append = function(data){
    if(!data){return;}
    let target = document.querySelector(this.options.element.lists_base);
    let html = this.list_adjust(data);
    if(!html){return;}
    target.insertAdjacentHTML('beforeend',html);
  };
  MAIN.prototype.list_change = function(data){
    if(!data){return;}
    let id = data.id;
    if(!id){return;}
    let target = document.querySelector(this.options.element.lists_base + "> *[data-id='"+id+"']");
    if(!target){return;}
    let html = this.list_adjust(data);
    if(!html){return;}
    target.insertAdjacentHTML('beforebegin',html);
    target.parentNode.removeChild(target);
    return true;
  };
  MAIN.prototype.list_adjust = function(data){
    if(!data){return;}
    if(typeof this.options.template[this.options.template_name.lists] === "undefined"){return;}
    let html = this.options.template[this.options.template_name.lists].html;
    let first_tag = this.options.element.template_first_tag || "*";
    let root_tag = this.options.template[this.options.template_name.lists].root_tag;
    let root_selector = root_tag + ".template "+ first_tag;
    let doc = new DOMParser().parseFromString("<"+root_tag+" class='template'>"+ html +"</"+root_tag+">", "text/html");
    for(let i in data){
      var elms = doc.querySelectorAll("*[data-name='"+i+"']");
      if(!elms && !elms.length){continue;}
      for(let j=0; j<elms.length; j++){
        elms[j].textContent = data[i];
      }
    }
    let elm = doc.querySelector(root_selector);
    if(elm){
      elm.setAttribute("data-id" , data.id);
    }
    return doc.querySelector(root_tag).innerHTML;
  };

  MAIN.prototype.check_empty_view = function(){
    let empty = document.querySelector(this.options.element.lists_empty);
    if(!empty){return;}
    let lists = document.querySelectorAll(this.options.element.lists_base + ">*");
    if(lists.length){
      empty.setAttribute("data-hidden" , "1");
    }
    else{
      empty.setAttribute("data-hidden" , "0");
    }
  };


  MAIN.prototype.click_lists = function(e){
    let target = e.target;
    if(!target){return;}
    let elm = new $$lib().upperSelector(target , this.options.element.lists_base + ">*");
    let id  = elm.getAttribute("data-id");
// console.log(id);

    switch(this.options.click_mode){
      case "modal" : this.view_modal(id);break;
      default      : this.click_lists_action(id);break;
    }
  };

  MAIN.prototype.click_lists_action = function(id){
    if(this.options.click_function){
      if(id && typeof this.options.datas[id] !== "undefined"){
        this.options.click_function(this.options.datas[id]);
      }
    }
    else{
      console.log(id);
    }
  };




  let LIB  = function(){};

  // 起動scriptタグを選択
  LIB.prototype.currentScriptTag = (function(){
    var scripts = document.getElementsByTagName("script");
    return this.currentScriptTag = scripts[scripts.length-1].src;
  })();

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
  LIB.prototype.construct = function(MAIN){
    switch(document.readyState){
      case "complete"    : new MAIN();break;
      case "interactive" : this.event(window , "DOMContentLoaded" , (function(MAIN){new MAIN()}).bind(this,MAIN));break;
      default            : this.event(window , "load"             , (function(MAIN){new MAIN()}).bind(this,MAIN));break;
		}
  };

  LIB.prototype.ymdhis2date = function(ymdhis){
    var y = ymdhis.substr(0,4);
    var m = ymdhis.substr(4,2);
    var d = ymdhis.substr(6,2);
    var h = ymdhis.substr(8,2);
    var i = ymdhis.substr(10,2);
    var s = ymdhis.substr(12,2);
    return y+"/"+m+"/"+d+" "+h+":"+i+":"+s;
  };


  return MAIN;
})()