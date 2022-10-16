;(function(){

	var __event = function(target, mode, func){
		if (target.addEventListener){target.addEventListener(mode, func, false)}
		else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
	};

	var __urlinfo = function(uri){
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

  var __upperSelector = function(elm , selector) {
    if(!elm || !selector){return;}
    for (var cur=elm; cur; cur=cur.parentElement) {
      if (cur.matches(selector)) {
        break;
      }
    }
    return cur;
  }

	var __construct = function(){
    switch(document.readyState){
      case "complete"    : new $$;break;
      case "interactive" : __event(window , "DOMContentLoaded" , function(){new $$});break;
      default            : __event(window , "load" , function(){new $$});break;
		}
  };

  var $$ = function(){

    var urlinfo = __urlinfo();

    // select-index
    var select_index = document.querySelector("select[name='indexes']");
    if(select_index){
      __event(select_index , "change" , (function(e){this.changeSelectIndex(e)}).bind(this));
      if(select_index.length === 1){
        select_index.style.setProperty("display","none","");
      }
      // else if(!urlinfo.query.index_value){
      //   var tfoot = document.querySelector(".table-responce table tfoot");
      //   tfoot.style.setProperty("display","none","");
      // }
    }

    // add-post
    var btn_addPost = document.querySelector(".table-responce table tfoot tr .add");
    if(btn_addPost){
      __event(btn_addPost , "click" , (function(e){this.clickAddPost(e)}).bind(this));
    }

    // edit-click
    var trs = document.querySelectorAll(".table-responce table tbody tr");
    for(var i=0; i<trs.length; i++){
      __event(trs[i] , "click" , (function(e){this.clickEdit(e)}).bind(this));
    }

    // edit-post
    var btn_editPosts = document.querySelectorAll(".table-responce table tbody tr th");
    for(var i=0; i<btn_editPosts.length; i++){
      __event(btn_editPosts[i] , "click" , (function(e){this.clickEditPost(e)}).bind(this));
    }

    // remove-button
    var btnRemove = document.querySelectorAll(".table-responce table tbody tr .close");
    for(var i=0; i<btnRemove.length; i++){
      __event(btnRemove[i] , "click" , (function(e){this.clickRemoveButton(e)}).bind(this));
    }
    
  };

  $$.prototype.changeSelectIndex = function(e){
    var target = e.currentTarget;
    var index_value = target.value;
    var urlinfo = __urlinfo();
    var querys = [];
    querys.push("p=" + urlinfo.query.p);
    querys.push("table_name=" + urlinfo.query.table_name);
    if(index_value){
      var jsonDec = encodeURIComponent(index_value);
      querys.push("index_value=" + jsonDec);
    }
    location.href = "?" + querys.join("&");
  };

  $$.prototype.clickAddPost = function(e){
    var urlinfo = __urlinfo();
    var tr = document.querySelector(".table-responce tfoot tr");

    // check
    var check = this.checkInputDatas_null(tr);
    if(check.status === "error"){
      alert(check.message);
      return;
    }

    // get-data
    var datas = this.getInputDatas(tr);
    var login_id = document.getElementById("login_id");
    datas["php"] = "\\mynt\\system\\database_table::ajax_postAdd('"+login_id.value+"','"+urlinfo.query.table_name+"')";
    new $$mynt_ajax({
      url : urlinfo.url,
      query : datas,
      onSuccess : (function(res){
        if(!res){return;}
        
// console.log(res);
        var json = JSON.parse(res);
        if(json.status === "error"){
          alert(json.message);
          return;
        }

        var querys = [];
        querys.push("p=" + urlinfo.query.p);
        querys.push("table_name="+ urlinfo.query.table_name);
        if(typeof urlinfo.query.index_value){
          querys.push("index_value="+ urlinfo.query.index_value);
        }

        location.href = urlinfo.url + "?" + querys.join("&");

        // var tbody  = document.querySelector(".table-responce tbody");
        // var tbody_trs = tbody.querySelectorAll(":scope > tr");
        // var footTr = document.querySelector(".table-responce tfoot tr");
        // var footTds = footTr.querySelectorAll(":scope > *");
        // var newTr  = document.createElement("tr");
        // __event(newTr , "click" , (function(e){this.clickEdit(e)}).bind(this));
        // for(var i=0; i<footTds.length; i++){
        //   var td;
        //   // th
        //   if(footTds[i].tagName === "TH"){
        //     td = document.createElement("th");
        //     var foot_button = footTds[i].querySelector("button.close");
        //     var new_button  = document.createElement("button");
        //     new_button.className = "close";
        //     new_button.type = "button";
        //     new_button.innerHTML = foot_button.innerHTML;
        //     __event(new_button , "click" , (function(e){this.clickRemoveButton(e)}).bind(this));
        //     td.appendChild(new_button);
        //     var span = document.createElement("span");
        //     span.className = "num";
        //     span.textContent += (tbody_trs.length + 1);
        //     td.appendChild(span);
        //     __event(td , "click" , (function(e){this.clickEditPost(e)}).bind(this));
        //   }
        //   // td
        //   else{
        //     td = document.createElement("td");
        //     var name   = footTds[i].getAttribute("data-name");
        //     var type   = footTds[i].getAttribute("data-type");
        //     var option = footTds[i].getAttribute("data-option");
        //     var length = footTds[i].getAttribute("data-length");
        //     td.setAttribute("data-name"   , name);
        //     td.setAttribute("data-type"   , type);
        //     td.setAttribute("data-option" , option);
        //     td.setAttribute("data-length" , length);
        //     var input = footTds[i].querySelector(":scope input,:scope textarea");
        //     if(input){
        //       td.innerHTML = input.value;
        //     }
        //     else if(option.toLowerCase().indexOf("auto_increment") !== -1){
        //       td.textContent = (typeof json.auto_increment !== "undefined") ? json.auto_increment : "--";
        //     }
        //   }
        //   newTr.appendChild(td);
        // }

        // tbody.appendChild(newTr);

        // // input-clear
        // this.clearInputs(footTr);

        // // number-set
        // this.setListNumber();
        
      }).bind(this)
    });
  };

  $$.prototype.clickEdit = function(e){
    if(e.target.matches(".close,.close *")){return;}

    var tr  = e.currentTarget;
    this.clickEditTr(tr);
  };

  $$.prototype.clickEditTr = function(tr){
    if(!tr){return;}

    if(__upperSelector(tr,"tbody")){
      if(tr.getAttribute("data-edit-flg") === "1"){return;}
      tr.setAttribute("data-edit-flg" , "1");
    }
    
    var tds = tr.querySelectorAll(":scope > td");
    var height = tr.offsetHeight -10;

    for(var i=0; i<tds.length; i++){
      var input;
      if(tds[i].getAttribute("data-type").toLowerCase() === "text"){
        input = document.createElement("textarea");
      }
      else{
        input = document.createElement("input");
      }
      input.name  = tds[i].getAttribute("data-name");
      var value   = tds[i].innerHTML;
      value = value.replace(/<br>/g,"\n");
      input.value = value;
      input.setAttribute("data-reset-value" , value);
      tds[i].textContent = "";
      var width = tds[i].offsetWidth -10;
      input.style.setProperty("width" , width  + "px","");
      input.style.setProperty("height", height + "px","");
      if(tds[i].getAttribute("data-option").toLowerCase().indexOf("auto_increment") !== -1){
        input.readOnly = true;
      }
      tds[i].appendChild(input);
    }
  };


  $$.prototype.clickEditPost = function(e){
    if(e.target.matches(".close,.close *")){console.log("close");return;}
    var urlinfo = __urlinfo();
    var th      = e.currentTarget;
    var tr      = th.parentNode;
    if(tr.getAttribute("data-edit-flg") !== "1"){return;}
    var datas = this.getInputDatas(tr);
    var login_id = document.getElementById("login_id");
    datas["php"] = "\\mynt\\system\\database_table::ajax_postEdit('"+login_id.value+"','"+urlinfo.query.table_name+"')";
    new $$mynt_ajax({
      url : urlinfo.url,
      query : datas,
      onSuccess : (function(res){
        if(!res){return;}
        var json = JSON.parse(res);
        if(json.status === "error"){
          alert(json.message);
        }
        this.input2text(tr);
      }).bind(this)
    });
  };

  $$.prototype.input2text = function(tr){
    if(!tr){return;}
    tr.removeAttribute("data-edit-flg");
    tds = tr.querySelectorAll("td");
    for(var i=0; i<tds.length; i++){
      var input = tds[i].querySelector(":scope input,:scope textarea");
      var value = input.value;
      value = value.replace(/\n/g,"<br>");
      tds[i].innerHTML = value;
    }
  };

  $$.prototype.clickRemoveButton = function(e){
    var rmBtn = e.currentTarget;

    if(!confirm("データを削除してもよろしいですか？この操作は取り消せません。")){return;}
    
    var urlinfo = __urlinfo();
    var tr = __upperSelector(rmBtn , "tr");
    var datas = this.getTrInnerDatas(tr);
    var login_id = document.getElementById("login_id");
    datas["php"] = "\\mynt\\system\\database_table::ajax_postRemove('"+login_id.value+"','"+urlinfo.query.table_name+"')";
    new $$mynt_ajax({
      url : urlinfo.url,
      query : datas,
      onSuccess : (function(tr,res){
        if(!res){return;}
// console.log(res);
        var json = JSON.parse(res);
        if(json.status === "error"){
          alert(json.message);
          return;
        }
        tr.parentNode.removeChild(tr);
        this.setListNumbers();
      }).bind(this,tr)
    });
    
  };

  $$.prototype.getInputDatas = function(tr){
    if(!tr || tr.tagName!=="TR"){return null;}
    var datas = {};
    var inputs = tr.querySelectorAll("input,textarea");
    for(var i=0; i<inputs.length; i++){
      var option = inputs[i].parentNode.getAttribute("data-option");
      datas["key["+inputs[i].name+"]"] = inputs[i].value;
    }
    return datas;
  };
  $$.prototype.getInputDatas_reset = function(tr){
    if(!tr || tr.tagName!=="TR"){return null;}
    var datas = {};
    var inputs = tr.querySelectorAll("input,textarea");
    for(var i=0; i<inputs.length; i++){
      var option = inputs[i].parentNode.getAttribute("data-option");
      datas["key["+inputs[i].name+"]"] = inputs[i].getAttribute("data-reset-value");
    }
    return datas;
  };

  $$.prototype.getTrInnerDatas = function(tr){
    if(!tr || tr.tagName!=="TR"){return null;}
    var datas = {};
    if(tr.getAttribute("data-edit-flg") === "1"){
      datas = this.getInputDatas_reset(tr);
    }
    else{
      var tds = tr.querySelectorAll("td");
      for(var i=0; i<tds.length; i++){
        var value = tds[i].innerHTML;
        value = value.replace(/<br>/g,"\n");
        datas["key["+tds[i].getAttribute("data-name")+"]"] = value;
      }
    }
    return datas;
  };

  $$.prototype.checkInputDatas_null = function(tr){
    if(!tr || tr.tagName!=="TR"){return {status:"error","message":"TRタグが指定されていません。"};}
    var inputs = tr.querySelectorAll("input,textarea");
    for(var i=0; i<inputs.length; i++){
      var option = inputs[i].parentNode.getAttribute("data-option");
      if(option.toLowerCase().indexOf("not null") !== -1 && inputs[i].value===""){
        return {status:"error","message":"必須項目です ("+inputs[i].name+")"};
      }
    }
    return {status:"ok",inputCount:inputs.length};
  }

  $$.prototype.clearInputs = function(tr){
    if(!tr || tr.tagName!=="TR"){return {status:"error","message":"TRタグが指定されていません。"};}
    var inputs = tr.querySelectorAll("input,textarea");
// console.log(inputs.length);
    for(var i=0; i<inputs.length; i++){
      inputs[i].value = "";
    }
  };

  $$.prototype.setListNumber = function(){
    var tbody_tr_ths = document.querySelectorAll(".table-responce tbody tr th:first-child");
    for(var i=0; i<tbody_tr_ths.length; i++){
      tbody_tr_ths.textContent = (i + 1);
      if(tbody_tr_ths.className === "add"){
        tbody_tr_ths.className = "";
      }
    }
  };
  $$.prototype.setListNumbers = function(){
    var tbody_trs = document.querySelectorAll(".table-responce tbody tr");
    for(var i=0; i<tbody_trs.length; i++){
      var num = tbody_trs[i].querySelector(":scope > th:first-child > .num");
      num.textContent = (i + 1);
    }
  };


	__construct();
})();
