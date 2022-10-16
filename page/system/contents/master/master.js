(function(){

  var __options = {
    tag_area  : ".list-area",
    tag_lists : ".lists",
    tag_list  : "tr",
    lists_count : 10,
    modal_buttons_value : {
      "del" : "削除",
      "add" : "追加",
      "edit" : "更新",
      "login" : "ログイン",
      "logout" : "ログアウト"
    },

    table_name : null
  };

  var MAIN = function(){
    __options.table_name = document.getElementById("master").value;
    this.loadTemplate();
    this.setEvent();
    this.view_select_master();
    this.check_no_data();
    // this.data_load();
    this.index_select();
    
  };

  MAIN.prototype.setEvent = function(){
    var btn_add = document.querySelectorAll("button.add");
    if(btn_add && btn_add.length){
      for(var i=0; i<btn_add.length; i++){
        new $$lib().event(btn_add[i] , "click" , (function(e){this.click_list(e)}).bind(this));
      }
    }

    var btn_import = document.querySelectorAll("button.csv-import");
    if(btn_import && btn_import.length){
      for(var i=0; i<btn_import.length; i++){
        new $$lib().event(btn_import[i] , "click" , (function(e){this.click_csv_import(e)}).bind(this));
      }
    }
    var btn_download = document.querySelectorAll("button.csv-download");
    if(btn_download && btn_download.length){
      for(var i=0; i<btn_download.length; i++){
        new $$lib().event(btn_download[i] , "click" , (function(e){this.click_csv_download(e)}).bind(this));
      }
    }

    var lists_area = document.querySelector(".lists table tbody.datas");
    if(lists_area){
      new $$lib().event(lists_area , "click" , (function(e){this.click_lists(e)}).bind(this));
    }

    var table_change = document.querySelectorAll(".select-master-area select[name='master']");
    if(table_change){
      for(var i=0; i<table_change.length; i++){
        new $$lib().event(table_change[i] , "change" , (function(e){this.master_table_change(e)}).bind(this));
      }
    }
  };

  MAIN.prototype.loadTemplate = function(){
    var page = document.getElementById("page").value;
    var urlinfo = new $$lib().urlinfo();
    var id = urlinfo.query.id || "";
    new $$ajax({
      url    : location.href,
      query : {
        php  : '\\lib\\master\\db::modal("div","'+ page +'","'+ __options.table_name +'","'+id+'")',
        exit : true
      },
      onSuccess : (function(res){
        if(!res){return;}
        this.template_modal = res;
      }).bind(this)
    });
  };

  MAIN.prototype.click_list = function(e){
    var id = "";
    this.view_modal(id);
  };
  MAIN.prototype.view_modal = function(id){
    if(!this.template_modal){
      alert("Error ! (code:0) templateデータが読み込まれていません。");
      return;
    }
    id = id ? id : "";

    var html = this.adjustTemplate(id);

    var buttons = this.getButtons(id);

    this.modal = new $$modal({
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
      title   : "データ登録",
      // [中断] メッセージ表示スタイル
      message : {
        html   : html,
        height : "auto",
        align  : "center"
      },
      // [下段] ボタン
      button  : buttons,
      // クリック挙動 [ "close" , "none" ]
      bgClick : "none"
      // loaded : (function(){this.setPasswordViewButton()}).bind(this)
    });
  };

  MAIN.prototype.getButtons = function(id){
    var buttons = [];

    // del
    buttons.push({
      text : __options.modal_buttons_value.del,
      click :(function(id){this.modal_button_del(id)}).bind(this , id)
    });

    // add
    buttons.push({
      text: id === "" ? __options.modal_buttons_value.add : __options.modal_buttons_value.edit,
      click :(function(){this.modal_button_add()}).bind(this)
    });

    return buttons;
  };

  MAIN.prototype.modal_button_add = function(){
    var datas = {};
    var forms = document.querySelectorAll(".modal-area input,.modal-area textarea,.modal-area select");
    for(var i in forms){
      datas[forms[i].name] = forms[i].value;
    }
    var page = document.getElementById("page").value;

    new $$ajax({
      url : location.href,
      query : {
        php        : '\\lib\\master\\db::data_save_html("'+page+'","'+ __options.table_name +'")',
        json       : JSON.stringify(datas),
        exit       : true
      },
      onSuccess : (function(res){
// console.log(res);
        if(res){
          var json = JSON.parse(res);
          if(json.status === "ok"){
            if(typeof json.html !== "undefined"){
              var lists = document.querySelector(".lists table tbody.datas");
              var tr_id = document.querySelector(".lists table tbody.datas tr[data-id='"+ json.data.id +"']");
              // modify
              if(tr_id){
                tr_id.insertAdjacentHTML("beforebegin" , json.html);
                lists.removeChild(tr_id);
              }
              // add
              else{
                lists.insertAdjacentHTML("beforeend" , json.html);
              }
            }
          }
          else if(json.message){
            alert(json.message);
            return;
          }
        }
        this.check_no_data();
        this.modal.close(); // modalを閉じる
      }).bind(this)
    });
  };

  MAIN.prototype.modal_button_del = function(id){
    if(!confirm("データを削除してもよろしいですか？この操作は取り消せません。")){return;}
    var page = document.getElementById("page").value;
    new $$ajax({
      url : location.href,
      query : {
        php  : '\\lib\\master\\db::data_del("'+page+'","'+ __options.table_name +'","'+id+'")',
        exit : true
      },
      onSuccess : (function(id , res){
        if(res){
          var json = JSON.parse(res);
          if(json.status === "ok"){
            var tr_id = document.querySelector(".lists table tbody.datas tr[data-id='"+ id +"']");
            // modify
            if(tr_id){
              tr_id.parentNode.removeChild(tr_id);
            }
          }
          else if(json.message){
            alert(json.message);
            return;
          }
        }
        this.check_no_data();
        this.modal.close(); // modalを閉じる
      }).bind(this , id)
    });
  };

  MAIN.prototype.adjustTemplate = function(id){
    var template = this.template_modal;
    if(id){
      var parser = new DOMParser();
      var doc    = parser.parseFromString(template, "text/html");
      var forms  = doc.querySelectorAll("input , textarea , select");
      for(var i=0; i<forms.length; i++){
        var name = forms[i].name;
        var td = document.querySelector(".lists table .datas tr[data-id='"+id+"'] td[data-name='"+name+"']");
        if(!td){continue;}
        var value = td.getAttribute("data-value") || "";
        switch(forms[i].type){
          case "hidden":
          case "text":
          case "number":
          // case "email":
          // case "url":
            forms[i].setAttribute("value" , td.textContent);
            break;

          case "textarea":
            forms[i].textContent = td.textContent;
            break;

          case "select-one":
            for(var j=0; j<forms[i].options.length; j++){
              if(forms[i].options[j].value === value){
                forms[i].options[j].setAttribute("selected" , true);
                break;
              }
            }
            break;

          default:
            forms[i].setAttribute("value" , td.textContent);
            break;
        }
        
      }
      template = doc.body.innerHTML;
    }
    return template;
  };

  MAIN.prototype.click_lists = function(e){
    var target = e.target;
    if(!target){return;}
    var tr = new $$lib().upperSelector(target , "tr");
    var id = tr.getAttribute("data-id");
    if(!id){return;}
    this.view_modal(id);
  };


  MAIN.prototype.view_select_master = function(){
    var select_master = document.querySelector("select[name='master']");
    if(select_master){
      var urlinfo = new $$lib().urlinfo();
      if(typeof urlinfo.query.master !== "undefined"){
        select_master.value = urlinfo.query.master;
      }
    }
  };

  MAIN.prototype.check_no_data = function(){
    // no-data
    var lists = document.querySelector(".lists");
    if(lists){
      var headers = lists.querySelectorAll("table thead tr th");
      var nodata  = lists.querySelector("tbody.no-data");
      var nodata_td = nodata.querySelector("td");
      if(headers.length && nodata_td){
        nodata_td.setAttribute("colspan" , headers.length);
      }
      var datas   = lists.querySelectorAll("table tbody.datas tr");
      if(!datas.length){
        nodata.style.setProperty("display","table-row-group","");
      }
      else{
        nodata.style.setProperty("display","none","");
      }
    }
  };


  MAIN.prototype.click_csv_import = function(e){
    // console.log("import");
    var page  = document.getElementById("page").value;
    var urlinfo = new $$lib().urlinfo();
    var master = urlinfo.query.master;
    if(!master){return;}
    var html = "<form class='csv-upload-form' method='post' enctype='multipart/form-data'>";
    html += "<input type='hidden' name='php' value='\\lib\\master\\upload::csv(\""+page+"\",\""+master+"\")'>";
    html += "<input type='hidden' name='redirect' value='?"+ location.href.split("?")[1] +"'>";
    html += "<input type='file' name='csv'>";
    html += "</form>";

    this.modal = new $$modal({
      // 表示サイズ
      size    : {
        width : "500px",
        height: "auto"
      },
      // 表示位置
      position : {
        vertical : "center",
        horizon  : "top",
        margin   : ["10px","10px","10px","10px"]
      },
      // 閉じるボタン
      close   : {
        html  : "",
        size  : 20,
        click : function(){}
      },
      // [上段] タイトル表示文字列
      title   : "データ登録",
      // [中断] メッセージ表示スタイル
      message : {
        html   : html,
        height : "auto",
        align  : "center"
      },
      // [下段] ボタン
      button  : [
        {
          text: "CSVデータをアップロード",
          click :(function(){
            var form = document.querySelector(".modal-area .modal-message .csv-upload-form");
            if(!form){return;}
            form.submit();
          }).bind(this)
        }
      ],
      // クリック挙動 [ "close" , "none" ]
      bgClick : "none"
      // loaded : (function(){this.setPasswordViewButton()}).bind(this)
    });
  };

  MAIN.prototype.click_csv_download = function(e){
    // console.log("download");
    var page = document.getElementById("page").value;
    // var urlinfo = new $$lib().urlinfo();
    // var table = urlinfo.query.master;
    var heads = this.lists2heads();
// console.log(heads);
    var datas = this.lists2datas();
// console.log(datas);
    new $$ajax({
      url : location.href,
      query : {
        // php   : '\\page\\'+page+'\\php\\master::csv_download()',
        php   : '\\lib\\master\\common::csv_download()',
        heads : JSON.stringify(heads),
        datas : JSON.stringify(datas),
        exit  : true
      },
      onSuccess : (function(res){
// console.log(res);
        var urlinfo = new $$lib().urlinfo();
        var table = urlinfo.query.master;
        const a = document.createElement('a');
        a.href = 'data:text/csv,' + encodeURIComponent(res);
        a.download = table + '.csv';
        a.style.display = 'none';
        document.body.appendChild(a); // ※ DOM が構築されてからでないとエラーになる
        a.click();
        document.body.removeChild(a);
      }).bind(this)
    });
  };

  MAIN.prototype.lists2heads = function(){
    var lists = document.querySelectorAll(".lists table thead tr.name th");
    var datas = [];
    for(var i=0; i<lists.length; i++){
      datas.push(lists[i].getAttribute("class"));
    }
    return datas;
  };
  MAIN.prototype.lists2datas = function(){
    var lists = document.querySelectorAll(".lists table tbody.datas tr");
    var datas = [];
    for(var i=0; i<lists.length; i++){
      var cells = lists[i].querySelectorAll("td");
      var arr = [];
      for(var j=0; j<cells.length; j++){
        arr.push(cells[j].textContent);
      }
      datas.push(arr);
    }
    return datas;
  };

  MAIN.prototype.data_load = function(){
    var where = this.get_index_where_json();
    if(where === "error"){return;}
// console.log(where);

    var page = document.getElementById("page").value;
    var urlinfo = new $$lib().urlinfo();
    var table = urlinfo.query.master;
    $$ajax({
      url : location.href,
      query : {
        php   : '\\lib\\master\\db::data_load_html("'+page+'","'+table+'")',
        where : where,
        exit  : true
      },
      onSuccess : (function(res){
        if(!res){
          return;
        }
        this.data_view(res);
        // this.pagenation(); ////////////////////////////////////////////////////////////////
      }).bind(this)
    });
  };
  MAIN.prototype.data_view = function(html){
    var lists = document.querySelector(".lists table tbody.datas");
    lists.insertAdjacentHTML("beforeend" , html);
    this.check_no_data();
  };
  MAIN.prototype.data_clear = function(){
    var area = document.querySelector(".lists tbody.datas");
    area.textContent = "";
  };

  MAIN.prototype.pagenation = function(){
    new $$pagenation({
      list_target   : ".lists table tbody.datas tr",
      list_count    : 20,
      target        : ".pagenation",
      page_count    : 5,
      query_current : "num",
      between_str   : "..",

      counter_reset_elm : ".counter-num",
      counter_reset_key : "num",

      show_prev_next : true,
      show_first_last : true
    });
  };

  MAIN.prototype.master_table_change = function(e){
    var target = e.target;
    if(!target){return;}
    
    var table_value = target.value;
    var urlinfo = new $$lib().urlinfo();
    urlinfo.query.master = table_value;
    var querys = [];
    for(var i in urlinfo.query){
      querys.push(i+"="+urlinfo.query[i]);
    }
    location.href = urlinfo.url +"?"+ querys.join("&");
  };

  MAIN.prototype.index_select = function(){
    // var uid  = document.getElementById("uid").value;
    var page = document.getElementById("page").value;
    var master = document.getElementById("master").value;
    new $$ajax({
      url : location.href,
      query : {
        php  : '\\lib\\data\\data::get_table_index_lists("'+page+'","'+master+'")',
        exit : true
      },
      onSuccess : (function(res){
        if(!res){
          this.index_select_hidden();
        }
        else{
          this.index = JSON.parse(res);
          this.index_select_form(this.index);
        }
        this.data_load();
      }).bind(this)
    });
  };

  MAIN.prototype.index_select_hidden = function(){
    var area = document.querySelector(".index-area");
    if(area){
      area.setAttribute("data-hidden","1");
    }
  };

  MAIN.prototype.index_select_form = function(datas){
    if(!datas || typeof datas.index === "undefined"){return;}

    var area = document.querySelector(".index-area .input");
    if(!area){return;}

    // var indexes = [];
    for(var i in datas.index){
      for(var j=0; j<datas.index[i].length; j++){
        var select_label = document.createElement("span");
        select_label.className = "label";
        select_label.textContent = datas.index[i][j];
        area.appendChild(select_label);
        var select_elm = document.createElement("select");
        select_elm.setAttribute("data-table-index" , i);
        select_elm.setAttribute("data-table-column" , datas.index[i][j]);
        new $$lib().event(select_elm , "change" , (function(e){this.index_select_change(e)}).bind(this));
        area.appendChild(select_elm);
        var first_option = document.createElement("option");
        first_option.setAttribute("class" , "first-option");
        first_option.value       = "";
        first_option.textContent = "*"+datas.index[i][j];
        select_elm.appendChild(first_option);
      }
    }
    // 最初のselectだけ値を挿入
    this.index_select_values(datas.datas);
    
  };
  MAIN.prototype.index_select_clear = function(key){
    var elm = document.querySelector(".index-area .input select[data-table-column='"+key+"']");
    if(!elm){return;}
    for(var i=elm.options.length-1; i>=1; i--){
      elm.remove(i);
    }
  };

  // 指定の上位1階層だけ値をセットする。
  MAIN.prototype.index_select_values = function(datas){
    if(!datas){return;}

    var area = document.querySelector(".index-area .input");
    if(!area){return;}

    var values = {};
    for(var i=0; i<datas.length; i++){
      for(var j=0; j<datas[i].key.length; j++){
        var k = datas[i].key[j];
        var v = datas[i].val[j];
        if(typeof values[k] === "undefined"){
          values[k] = [];
        }
        if(values[k].indexOf(v) === -1){
          values[k].push(v);
        }
      }
    }
    for(var j in values){
      var target = area.querySelector("select[data-table-column='"+j+"']");
      if(!target){continue;}
      this.index_select_clear(j);
      for(var k=0; k<values[j].length; k++){
        var option = document.createElement("option");
        option.value       = values[j][k];
        option.textContent = values[j][k];
        target.appendChild(option);
      }
    }
  };

  MAIN.prototype.index_select_change = function(e){
    var target = e.target;
    if(!target){return}
    var index_value = target.getAttribute("data-table-index");
    if(!index_value){return;}

    var area = document.querySelector(".index-area .input");
    if(!area){return;}

    var before_flg = 0;
    //次の項目の内容を入れ替える
    for(var i in this.index.index){
      var elms = area.querySelectorAll("select[data-table-index='"+i+"']");
      if(!elms){break;}

      if(i === index_value){before_flg++;}
      if(!before_flg){continue;}

      var vals = [];
      for(var j=0; j<elms.length; j++){
        if(elms[j].value === ""){continue;}
        vals.push(elms[j].value);
      }
      if(vals.length === elms.length){
        var under_datas = this.index_select_search_datas(this.index.datas , i , vals);
        if(under_datas){
          this.index_select_values(under_datas);
        }
      }
    }
    this.data_clear();
    this.data_load();
    return null;
  };


  MAIN.prototype.index_select_search_datas = function(datas , index , vals){
    if(!datas){return;}
    for(var i in datas){
      if(datas[i].index === index){
        if(datas[i].val.join("_") === vals.join("_")){
          return datas[i].data;
        }
      }
      else{
        if(typeof datas[i].data === "object"){
          return this.index_select_search_datas(datas[i].data , index , vals);
        }
      }
    }
  };

  MAIN.prototype.get_index_where = function(){
    var selects = document.querySelectorAll(".index-area .input select");
    if(!selects || !selects.length){return null;}
    var arr = {};
    for(var i=0; i<selects.length; i++){
      var key = selects[i].getAttribute("data-table-column");
      if(!key){continue;}
      arr[key] = selects[i].value;
    }
    return arr;
  };
  MAIN.prototype.get_index_where_json = function(){
    var data = this.get_index_where();
    if(data === null){
      return "";
    }
    else{
      for(var i in data){
        if(data[i] === ""){return "error";}
      }
      return JSON.stringify(data);
    }
    
  };


  new $$lib().construct(MAIN);
})();