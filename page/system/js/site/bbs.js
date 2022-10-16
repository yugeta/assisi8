;(function(){
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
      uri      : uri,
			url      : sp.join("/"),
      dir      : sp.slice(0 , sp.length-1).join("/") +"/",
      file     : sp.pop(),
		  domain   : sp[2],
      protocol : sp[0].replace(":",""),
      hash     : (urls_hash[1]) ? urls_hash[1] : "",
			query    : (urls_query[1])?(function(urls_query){
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
  
  var $$ = function(){

		switch(document.readyState){
      case "complete":
        this.start();
        break;
      case "interactive":
        $$event(window , "DOMContentLoaded" , (function(e){this.start(e)}).bind(this));
        break;
      default:
        $$event(window , "load" , (function(e){this.start(e)}).bind(this));
        break;
    }
	};

	$$.prototype.start = function(){
    var comments = document.querySelectorAll("#datas .comment pre");
    for(var i=0; i<comments.length; i++){
      $$event(comments[i] , "click" , (function(e){this.toggleCommentExpand(e)}).bind(this));
    }

    var typeSelect = document.querySelector(".type-area select[name='types']");
    if(typeSelect){
      $$event(typeSelect , "change" , function(e){
        var typeSelect = e.currentTarget;
        var urlinfo = $$urlinfo();
        if(typeSelect.value !== ""){
          location.href = urlinfo.url +"?p="+urlinfo.query.p+"&type="+typeSelect.value;
        }
        else{
          location.href = urlinfo.url +"?p="+urlinfo.query.p;
        }
      });
      if(!typeSelect.value){
        document.querySelector(".input-form").style.setProperty("display","none","");
        document.querySelector(".message").setAttribute("data-message-view","1");
      }
      else{
        document.querySelector(".input-form").style.setProperty("display","block","");
      }
    }

    var backButton = document.querySelector("button[value='back']");
    if(backButton){
      $$event(backButton , "click" , (function(e){
        this.pageBack2rootLists();
      }).bind(this));
    }

    var trashButtons = document.querySelectorAll("#datas .trash");
    if(trashButtons.length){
      for(var i=0; i<trashButtons.length; i++){
        $$event(trashButtons[i] , "click" , (function(e){this.clickTrashButton(e)}).bind(this));
      }
    }
  }

  $$.prototype.pageBack2rootLists = function(){
    var urlinfo = $$urlinfo();
    location.href = urlinfo.url+"?p=" + urlinfo.query.p +"&type=" + urlinfo.query.type;
  };

  $$.prototype.toggleCommentExpand = function(e){
    var target = e.currentTarget;
    if(!target){
      return;
    }
    else if(target.getAttribute("data-expand") !== "1"){
      target.setAttribute("data-expand" , '1');
    }
    else{
      target.setAttribute("data-expand" , "0");
    }
  };

  $$.prototype.clickTrashButton = function(e){
    if(!confirm("データを削除してもよろしいですか？※この操作は取り消せません。")){return;}
    var target  = e.currentTarget;
    var id      = target.getAttribute("data-id");
    var yuan    = target.getAttribute("data-yuan");
    var urlinfo = $$urlinfo();
    new $$MYNT_AJAX({
      url : urlinfo.url,
      query : {
        method_return : "\\MYNT\\SYSTEM\\bbs::clickTrashButton",
        type : urlinfo.query.type,
        id   : id,
        yuan : yuan
      },
      onSuccess : (function(res){
        if(!res){return;}
        var json = JSON.parse(res);
        this.getElementRemoveTarget(json.id , json.yuan);
      }).bind(this)
    });
  };

  $$.prototype.getElementRemoveTarget = function(id,yuan){
    var urlinfo = $$urlinfo();
    // rootを消した場合は、戻る処理
    if(!yuan && typeof urlinfo.query.id !== "undefined" && urlinfo.query.id){
      this.pageBack2rootLists();
    }
    // 項目を表示削除
    else{
      var target = document.querySelector("#datas .bbs[data-id='"+id+"'][data-yuan='"+yuan+"']");
      if(!target){return;}
      target.parentNode.removeChild(target);
    }
  };

  new $$;
})();