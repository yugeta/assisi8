;$$upload = (function(){

  var MAIN = function(options){
    this.options = options ? options : null;

    this.set_files();

    var elm_upload_images = document.querySelector("button.upload-images");
    if(elm_upload_images){
      // new LIB().event(elm_upload_images , "click" , (function(e){this.set_upload_images(e)}).bind(this));
      this.set_upload_images();
    }
    // var elm_upload_sounds = document.querySelector("button.upload-sounds");
    // if(elm_upload_sounds){
    //   this.set_upload_sounds();
    // }
    // var elm_upload_videos = document.querySelector("button.upload-videos");
    // if(elm_upload_videos){
    //   this.set_upload_videos();
    // }
    var elm_upload_files = document.querySelector("button.upload-files");
    if(elm_upload_files){
      this.set_upload_files();
    }

    if(typeof window.flg_upload === "undefined"){
      new LIB().event(window        , "scroll" , (function(e){this.check_scroll(e)}).bind(this));
      new LIB().event(window        , "resize" , (function(e){this.check_scroll(e)}).bind(this));
      new LIB().event(window        , "click" , (function(e){this.click_upload(e)}).bind(this));
      // this.event(window              , "click" , (function(e){this.click_upload(e)}).bind(this));
      window.flg_upload = true;
    }
    if(typeof document.body.flg_upload === "undefined"){
      new LIB().event(document.body , "scroll" , (function(e){this.check_scroll(e)}).bind(this));
      document.body.flg_upload = true;
    }

    // mode-select
    var modes = document.querySelector("select.mode-select");
    if(modes){
      new LIB().event(modes , "change" , (function(e){this.change_mode(e)}).bind(this));
    }

    // files-item-click
    let files = document.querySelector(".files");
    if(files){
      new LIB().event(files , "click" , this.click_files.bind(this))
    }

  };

  // MAIN.prototype.event = function(target, mode, func , flg){
  //   flg = (flg) ? flg : false;
	// 	if (target.addEventListener){target.addEventListener(mode, func, flg)}
	// 	else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
  // };

  MAIN.prototype.datas = null;

  MAIN.prototype.set_files = function(lastFile){
    this.datas = null;
    var elm = document.querySelector(".files");
    if(!elm){return;}
    lastFile = lastFile ? lastFile : "";
    var urlinfo = new LIB().urlinfo();
    new $$ajax({
      url : urlinfo.url,
      query : {
        php : "\\page\\system\\contents\\media\\view::files(\""+lastFile+"\")",
        exit : true
      },
      onSuccess : (function(res){
        if(!res){return;}
        var data = JSON.parse(res);
// console.log(data);
        if(typeof data.dir   === "undefined"
        || typeof data.files === "undefined"
        // || !data.files.length
        ){return;}
        this.view_files(data.dir , data.files , data.nextCount);
      }).bind(this)
    });

  };

  MAIN.prototype.view_files = function(dir , files , next){
    var target_area = this.getTargetArea();
    if(!target_area){return;}
    if(!files.length){
      target_area.removeAttribute("data-loading");
      return;
    }
    

    for(var i=0; i<files.length; i++){
      this.add_viewFile_after(dir , files[i]);
    }
    // console.log("file-viewed.");

    // next-view?
    if(next){
      let file = files[files.length-1].currentName +"."+ files[files.length-1].extension;
      if(this.check_loading() === true){
        this.datas = null;
        setTimeout((function(file){this.set_files(file);}).bind(this,file),1000);
      }
      else{
        this.datas = {
          dir : dir,
          lastFile : file
        };
      }
    }
    // loading非表示
    else{
      this.datas = null;
      target_area.removeAttribute("data-loading");
    }
  };

  MAIN.prototype.check_loading = function(){
    var target_area = this.getTargetArea();
    if(!target_area){return;}
    var rect = target_area.getBoundingClientRect();
    var target_bottom = rect.bottom;
    var page_bottom   = window.innerHeight + document.body.scrollTop;
    if(target_bottom < page_bottom){
      return true;
    }
    else{
      return false;
    }
  };

  MAIN.prototype.check_scroll = function(e){
    if(this.datas === null){return;}
    if(this.check_loading() === true){
// console.log(this.datas.dir+"/"+this.datas.lastFile);
      this.set_files(this.datas.lastFile);
    }
  };


  MAIN.prototype.getTargetArea = function(){
    return document.querySelector(".files");
  };

  // 先頭に追加
  MAIN.prototype.add_viewFile_before = function(dir , data){
    target_area = this.getTargetArea();
    if(!dir || !data){return;}
    // let id = file.split(".");
    let file = data.currentName +"."+ data.extension;
    let img  = document.createElement("img");
    let ext  = data.extension;
    switch(ext){
      case "jpg":
      case "jpeg":
      case "gif":
      case "png":
      case "svg":
        img.className = "img";
        img.src = this.setTemplatePath(dir + file);
        img.setAttribute("data-id" , data.currentName);
        img.setAttribute("data-dir"  , dir);
        img.setAttribute("data-file" , file);
        if(target_area.firstChild){
          target_area.insertBefore(img , target_area.firstChild);
        }
        else{
          target_area.appendChild(img);
        }
        break;
      default:
        var div = document.createElement("div");
        div.className = "file";
        div.setAttribute("data-id"   , data.currentName);
        div.setAttribute("data-dir"  , dir);
        div.setAttribute("data-file" , file);
        div.innerHTML = "<span class='name'>"+ data.fileName +"</span><span class='ext'>"+ ext +"</span>";
        if(target_area.firstChild){
          target_area.insertBefore(div , target_area.firstChild);
        }
        else{
          target_area.appendChild(div);
        }
        break;
    }
    return true;
  };
  // 先頭に追加
  MAIN.prototype.add_viewFile_before_file = function(dir , file){
    target_area = this.getTargetArea();
    if(!dir || !file){return;}
    var id = file.split(".");
    var img = document.createElement("img");
    img.className = "img";
    img.src = this.setTemplatePath(dir + file);
    img.setAttribute("data-dir"  , dir);
    img.setAttribute("data-file" , file);
    img.setAttribute("data-id" , id[0]);
    target_area.insertBefore(img , target_area.firstChild);
    return true;
  };
  // 最後に追加
  MAIN.prototype.add_viewFile_after = function(dir , data){
    target_area = this.getTargetArea();
    if(!dir || !data){return;}
    let file = data.currentName +"."+ data.extension;
    var id  = file.split(".");
    let ext = id[1];
    switch(ext){
      case "jpg":
      case "jpeg":
      case "gif":
      case "png":
      case "svg":
        var img = document.createElement("img");
        img.className = "img";
        img.src = this.setTemplatePath(dir + file);
        img.setAttribute("data-id" , id[0]);
        img.setAttribute("data-dir"  , dir);
        img.setAttribute("data-file" , file);
        target_area.appendChild(img);
        break;

      default:
        var div = document.createElement("div");
        div.className = "file";
        div.setAttribute("data-id"   , id[0]);
        div.setAttribute("data-dir"  , dir);
        div.setAttribute("data-file" , file);
        div.innerHTML = "<span class='name'>"+ data.fileName +"</span><span class='ext'>"+ ext +"</span>";
        target_area.appendChild(div);
        break;
    }
    return true;
  };

  MAIN.prototype.setTemplatePath = function(path,size){
    size = size ? size : 150;
    var arr = [
      "mode=thumbnail",
      "file=" + path,
      "w=" + size,
      "h=0"
    ];
    return "image.php?" + arr.join("&");
  };
  MAIN.prototype.setTemplatePath_encode = function(path,size){
    size = size ? size : 150;
    var arr = [
      "mode=thumbnail",
      "file=" + path,
      "w=" + size,
      "h=0"
    ];
    return "image.php?q=" + btoa(arr.join("&"));
  };


  MAIN.prototype.set_upload_images = function(){
    // var urlinfo = new LIB().urlinfo();
    // var page = document.getElementById("page").value;
    
    new $$fileupload_image({
      url : location.href,
      querys       : {
        php : '\\page\\system\\contents\\media\\upload::up_image()',
        exit : true,
        size : 300
      },
      btn_selector : "button.upload-images",
      flg_icon_comment : false,
      contentTypes  : ["image/gif" , "image/jpeg" , "image/png" , "image/svg+xml"],
      file_select  : function(res , options){},
      post_success : (function(res , options){
        if(!res){return;}

        var data = JSON.parse(res);
        if(typeof data.dir === "undefined" || typeof data.file === "undefined"){return;}
        this.add_viewFile_before_file(data.dir , data.file);
      }).bind(this),
      post_finish  : function(res , options){
        console.log("finished !!!");
      },
      post_error   : function(res , options){console.log(res);}
    });
  };
  MAIN.prototype.set_upload_sounds = function(){return;
    new $$fileupload_sound({
      url : location.href,
      querys       : {
        php : '\\page\\system\\contents\\media\\upload::up_sound()',
        exit : true,
        size : 300
      },
      btn_selector : "button.upload-sounds",
      flg_icon_comment : false,
      contentTypes  : ["audio/mp3"],
      file_select  : function(res , options){},
      post_success : (function(res , options){
        if(!res){return;}
        var data = JSON.parse(res);
        if(typeof data.dir === "undefined" || typeof data.file === "undefined"){return;}
        this.add_viewFile_before_file(data.dir , data.file);
      }).bind(this),
      post_finish  : function(res , options){
        console.log("finished !!!");
      },
      post_error   : function(res , options){console.log(res);}
    });
  };
  MAIN.prototype.set_upload_videos = function(){return;
    new $$fileupload_video({
      url : location.href,
      querys       : {
        php : '\\page\\system\\contents\\media\\upload::up_video()',
        exit : true,
        size : 300
      },
      btn_selector : "button.upload-videos",
      flg_icon_comment : false,
      contentTypes  : ["video/mp4"],
      file_select  : function(res , options){},
      post_success : (function(res , options){
        if(!res){return;}
        var data = JSON.parse(res);
        if(typeof data.dir === "undefined" || typeof data.file === "undefined"){return;}
        this.add_viewFile_before_file(data.dir , data.file);
      }).bind(this),
      post_finish  : function(res , options){
        console.log("finished !!!");
      },
      post_error   : function(res , options){console.log(res);}
    });
  };
  MAIN.prototype.set_upload_files = function(){
    if(typeof $$fileupload === "undefined"){return;}
    new $$fileupload({
      url : location.href,
      querys       : {
        php : '\\page\\system\\contents\\media\\upload::up_file()',
        exit : true
      },
      btn_selector : "button.upload-files",
      file_select  : function(res , options){
        // console.log(res);
      },
      post_success : (function(res , options){
// console.log("success : "+res);
        if(!res){return;}
        let data = JSON.parse(res);
// console.log(data);
        if(data.status !== "ok"){
          alert("正常にアップロードできませんでした。");
          console.log(data);
          return;
        }
        let service_name = document.getElementById("page").value;
        let dir = "data/"+ service_name +"/media/";
        this.add_viewFile_before(dir , data.data);
      }).bind(this),
      post_finish : function(res , options){console.log("finished !!!");},
      post_error : function(res , options){console.log(res);}

      // url : location.href,
      // querys       : {
      //   php : '\\page\\system\\contents\\media\\upload::up_file()',
      //   exit : true,
      //   size : 300
      // },
      // btn_selector : "button.upload-files",
      // flg_icon_comment : false,
      // contentTypes  : [],
      // file_select  : function(res , options){},
      // post_success : (function(res , options){
      //   console.log(res);
      //   // if(!res){return;}
      //   // var data = JSON.parse(res);
      //   // if(typeof data.dir === "undefined" || typeof data.file === "undefined"){return;}
      //   // this.add_viewFile_before_file(data.dir , data.file);
      // }).bind(this),
      // post_finish  : function(res , options){
      //   console.log("finished !!!");
      // },
      // post_error   : function(res , options){console.log(res);}
    });
  };


  MAIN.prototype.click_upload = function(e){
    let target_item = new $$lib().upperSelector(e.target , ".ImageDialog-area .files > *");
    if(!target_item){return;}
    let target_className = target_item.className;
    switch(target_className){
      case "sound":
        console.log("sound");
        break;

      case "video":
        console.log("video");
        break;

      case "img":
        this.click_image(target_item);
        break;

      case "file":
        this.click_file(target_item);
        break;
    }
  };
  MAIN.prototype.click_image = function(target){
    if(!target){return;}
    // var target = e.target;
    // if(!new LIB().upperSelector(target , ".files")){return;}

    var id = target.getAttribute("data-id");
    if(!id){return;}

    var sp = id.split(".");
    var filename  = sp[0];
    // var extension = sp[1];

    if(this.options
    && typeof this.options.image_click !== "undefined"){
      this.options.image_click(filename);
    }
    else{
      this.modal_image_info(filename);
    }
    
  };
  MAIN.prototype.click_file = function(target){
    if(!target){return;}
    var dir  = target.getAttribute("data-dir");
    var file = target.getAttribute("data-file");
    if(!dir||!file){return;}
    if(this.options
    && typeof this.options.file_click !== "undefined"){
      let data = {
        dir : dir,
        file : file
      };
      this.options.file_click(data);
    }
  };

  MAIN.prototype.modal_image_info = function(filename){
    var html = this.modal_html(filename);

    new $$modal({
      // 表示サイズ
      size    : {
        width : "500px",
        height: "auto"
      },
      // 表示位置
      position : {
        vertical : "top",     // 縦 [top , *center(*画像などがある場合はサイズ指定してから使用すること) , bottom]
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
      title   : "Title",
      // [中断] メッセージ表示スタイル
      message : {
        html   : html,
        height : "auto",
        align  : "center"
      },
      // [下段] ボタン
      button  : [
        {
          text:"削除",
          click : (function(filename){
            if(!confirm("このファイルをサーバーから削除してもよろしいですか？この操作は取り消せません。")){return;}
            console.log("remove");
            this.remove_image(filename);
          }).bind(this,filename)
        },
        {
          mode:"close",
          text:"閉じる",
          click : function(){}
        }
      ],
      // クリック挙動 [ "close" , "none" ]
      bgClick : "close",

      loaded : function(){
        // console.log("loaded!!");
      }
    });
  };

  MAIN.prototype.get_modalImage = function(filename){
    if(!filename){return "";}
    return document.querySelector(".files img[data-id='"+filename+"']");
  };

  MAIN.prototype.modal_html = function(filename){
    if(!filename){return "";}
    var img = this.get_modalImage(filename);
    if(!img){return "";}
    // var srcinfo = new LIB().urlinfo(img.getAttribute("src"));
    var dir  = img.getAttribute("data-dir");
    var file = img.getAttribute("data-file");
    var html = "<img src='"+ dir + file +"'>";
    return html;
  };

  MAIN.prototype.remove_image = function(filename){
    var urlinfo = new LIB().urlinfo();
    new $$ajax({
      url : location.href,
      query : {
        php : '\\lib\\media\\data::remove("'+filename+'")',
        exit : true
      },
      onSuccess : (function(res){
        if(!res){return;}
        var json = JSON.parse(res);
        if(!json || typeof json.status === "undefined" || json.status !== "ok"){return;}
        // var img = this.get_modalImage(json.id);
        let file = document.querySelector(".files > *[data-id='"+json.id+"']");
// console.log(file);
        if(!file){return;}
        file.parentNode.removeChild(file);
      }).bind(this)
    });
  };

  MAIN.prototype.change_mode = function(e){
    var target = e.target;
    if(!target){return;}
// console.log(target);
  };

  MAIN.prototype.click_files = function(e){
    let ImageDialog_bg = new LIB().upperSelector(e.target , ".ImageDialog-bg");
    // blog等の処理のため、ダイアログ表示なし
    if(ImageDialog_bg){return;}

    let item = new LIB().upperSelector(e.target , ".files > .file,.files > .img");
    if(!item){return;}
    // console.log(item);
    let type = item.className;
    let dir  = item.getAttribute("data-dir");
    let file = item.getAttribute("data-file");
    this.view_item_modal(dir , file);
  };
  MAIN.prototype.view_item_modal = function(dir , file){
    if(!dir || !file){return;}

    let file_sp = file.split(".");
    let ext     = file_sp[1].toLowerCase();
    let path    = dir+file;

    let html = "<div class='path'>"+ path +"</div>";
    switch(ext){
      case "jpg":
      case "jpeg":
      case "gif":
      case "png":
      case "svg":
        html += "<img src='"+ path +"'>";
        break;
      case "mp4":
        html += "<video autoplay muted playsinline loop controls src='"+ path +"'>";
        html += "</video>";
        break;
    }

    // responcive
    let margin = window.innerWidth < 500 ? ["100px","10px","10px","10px"] : ["100px","10px","10px","200px"];
    new $$modal({
      // 表示サイズ
      size    : {
        width : "500px",
        height: "auto"
      },
      // 表示位置
      position : {
        vertical : "top",     // 縦 [top , *center(*画像などがある場合はサイズ指定してから使用すること) , bottom]
        horizon  : "center",  // 横 [left , *center , right]
        margin   : margin   // [上、右、下、左]
      },
      // 閉じるボタン
      close   : {
        html  : "",
        size  : 20,
        click : function(){}
      },
      // [上段] タイトル表示文字列
      title   : "Title",
      // [中断] メッセージ表示スタイル
      message : {
        html   : html,
        height : "auto",
        align  : "center"
      },
      // [下段] ボタン
      button  : [
        {
          mode  : "close",
          text  : "削除",
          click : (function(path){
            if(!confirm("このファイルをサーバーから削除してもよろしいですか？この操作は取り消せません。")){return;}
// console.log("remove");
            this.remove_image(path);
          }).bind(this , path)
        },
        {
          mode  : "close",
          text  : "閉じる",
          click : (function(res){
            console.log(res);
          }).bind(this)
        }
      ],
      // クリック挙動 [ "close" , "none" ]
      bgClick : "close",

      loaded : (function(){
        // console.log("loaded!!");
      }).bind(this)
    });
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
      case "complete":
        new MAIN();
        break;
      default:
        this.event(window , "load" , (function(){
          new MAIN()
        }).bind(this));
        break;
		}
  };


  // new LIB().construct();

  return MAIN;
})();