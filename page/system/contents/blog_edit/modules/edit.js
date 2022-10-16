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
		// add-tag
		this.setEvent_addTag();

		// add-eyecatch
		this.setEvent_eyecatch();

		// preview
		this.viewPreview();

		// Remove
		this.setRemove();

		// keyboard
		this.keyboard();

		// textarea（記事のソース表示）
		// this.setSource();

		// ソースコード挿入ボタン
		this.setEvent_addTag_code_btn();

		// アイキャッチ[追加/削除]ボタン表示切替
		if(document.querySelector("#eyecatch > img.eycatch_img").getAttribute("src")){
			document.getElementById("eyecatch_add").style.setProperty("display","none","");
			document.getElementById("eyecatch_del").style.setProperty("display","inline-block","");
		}
		else{
			document.getElementById("eyecatch_add").style.setProperty("display","inline-block","");
			document.getElementById("eyecatch_del").style.setProperty("display","none","");
		}
	};

	MAIN.prototype.changeSelect = function(event){
		var target = event.target;
		var urlData = this.urlinfo();
		var url = urlData.url+"?b="+urlData.query.b+"&p="+urlData.query.p+"&file="+target.value;
		location.href = url;
	};

	MAIN.prototype.urlinfo=function(uri){
		if(!uri){uri = location.href;}
		var data={};
		//URLとクエリ分離分解;
		var query=[];
		if(uri.indexOf("?")!=-1){query = uri.split("?")}
		else if(uri.indexOf(";")!=-1){query = uri.split(";")}
		else{
			query[0] = uri;
			query[1] = '';
		}
		//基本情報取得;
		var sp = query[0].split("/");
		var data={
			url:query[0],
			dir:this.pathinfo(uri).dirname,
			domain:sp[2],
			protocol:sp[0].replace(":",""),
			query:(query[1])?(function(q){
				var data=[];
				var sp = q.split("&");
				for(var i=0;i<sp .length;i++){
					var kv = sp[i].split("=");
					if(!kv[0]){continue}
					data[kv[0]]=kv[1];
				}
				return data;
			})(query[1]):[],
		};
		return data;
	};
	MAIN.prototype.pathinfo = function(p){
		var basename="",
		    dirname=[],
				filename=[],
				ext="";
		var p2 = p.split("?");
		var urls = p2[0].split("/");
		for(var i=0; i<urls.length-1; i++){
			dirname.push(urls[i]);
		}
		basename = urls[urls.length-1];
		var basenames = basename.split(".");
		for(var i=0;i<basenames.length-1;i++){
			filename.push(basenames[i]);
		}
		ext = basenames[basenames.length-1];
		return {
			"hostname":urls[2],
			"basename":basename,
			"dirname":dirname.join("/"),
			"filename":filename.join("."),
			"extension":ext,
			"query":(p2[1])?p2[1]:"",
			"path":p2[0]
		};
	};


	MAIN.prototype.setImageButton = function(mode,selectedImage){
		selectedImage = selectedImage ? selectedImage : "";
		var tempfile = "page/system/contents/blog_edit/html/upload_modal.html";

		new $$ajax({
			url: new LIB().urlinfo(location.href).url,
			method:"POST",
			async:true,
			query:{
				php    : '\\lib\\html\\file::getFile("'+ tempfile +'")',
				mode   : mode,
				exit   : true
			},
			onSuccess:(function(mode , source){this.setImageDialog_temp(mode , source)}).bind(this , mode)
		});

		// var source = "<div class='ImageDialog-area'><div class='files'></div></div>";
		// this.setImageDialog_temp(source);
	};

	MAIN.prototype.setImageDialog_temp = function(mode , source){

		// dialog-view
		var bg = document.createElement("div");
		bg.className = "ImageDialog-bg";
		bg.style.setProperty("top" , window.pageYOffset + "px" , "");
		bg.innerHTML = source;
		document.body.appendChild(bg);

		// 削除ボタン
		var button_close = document.querySelector(".image-pickup-dialog-close");
		if(button_close){
			new LIB().event(button_close , "click" , (function(e){this.image_lists_close(e)}).bind(this));
		}
// console.log("momde : "+mode);
		// // 画像一覧表示
		// switch(mode){
		// 	case "imgTag":
		// 		new $$upload({
		// 			image_click : (function(filename){
		// 				this.modal_image_info(filename);
		// 			}).bind(this)
		// 		});
		// 		break;

		// 	case "eyecatch":
		// 		new $$upload({
		// 			image_click : (function(filename){
		// 				this.setEvent_selectEyecatch(filename);
		// 			}).bind(this)
		// 		});
		// 		break;

		// }
		// console.log(mode);
		this.upload = new $$upload({
			mode : mode,
			image_click : (function(filename){
				var mode = this.upload.options.mode;
				switch(mode){
					case "imgTag":
						this.modal_image_info(filename);
						break;
					case "eyecatch":
						this.setEvent_selectEyecatch(filename);
						break;
				}
			}).bind(this)
		});
		
	};

	MAIN.prototype.image_lists_close = function(){
		// 画像ダイアログの削除
		var target = document.querySelector(".ImageDialog-bg");
		if(target){
			target.parentNode.removeChild(target);
		}
		
		// input-fileの削除
		var input = document.querySelector("input[type='file']");
		if(input){
			input.parentNode.removeChild(input);
		}
	};
	

	MAIN.prototype.setEvent_imagesDialogSelect = function(){
		// images-click-proc
		var pics = document.getElementsByClassName("pictures");

		// imageサムネイルのクリック処理
		for(var i=0; i<pics.length; i++){
			pics[i].onclick = (function(e){this.setEvent_picsClick(e)}).bind(this);
		}
	};

	MAIN.prototype.setIframeTag = function(){
		var form     = document.createElement("form");
		form.name    = "form1";
		form.method  = "post";
		form.enctype = "multipart/form-data";
		var fl = new LIB().urlinfo(location.href);
		form.action  = fl.filename +"."+ fl.extension;

		var inp0     = document.createElement("input");
		inp0.type    = "hidden";
		inp0.name    = "mode";
		inp0.value   = "picture";

		var inp2     = document.createElement("input");
		inp2.type    = "hidden";
		inp2.name    = "method_return";
		inp2.value   = "\\MYNT\\SYSTEM\\UPLOAD::setPost";

		var inp1     = document.createElement("input");
		inp1.id      = "input_file";
		inp1.type    = "file";
		inp1.name    = "data[]";
		inp1.multiple= "multiple";
		inp1.onchange = function(){this.form.submit()};

		form.appendChild(inp0);
		form.appendChild(inp1);
		form.appendChild(inp2);

		var img_upload_iframe = document.getElementById("img_upload_iframe");
		var d = img_upload_iframe.contentWindow.document;
		d.body.appendChild(form);

		// var scr1     = document.createElement("script");
		// scr1.type    = "text/javascript";
		// scr1.src     = "system/js/common.js";
		// d.body.appendChild(scr1);
    //
		// var meta1     = document.createElement("meta");
		// meta1.setAttribute("charset" , "utf-8");
		// d.head.appendChild(meta1);

		// this.setPictureImages();
	};
	// MAIN.prototype.setPictureImages = function(){
	// 	var pictures = document.getElementById("pictures");
	// 	if(pictures === null){return}

	// 	// サーバーからデータリストの読み込み
	// 	new $$ajax({
	// 		url:new LIB().urlinfo(location.href).path,
	// 		method:"POST",
	// 		async:true,
	// 		query:{
	// 			method_return:"\\MYNT\\SYSTEM\\UPLOAD::viewImages",
	// 			lastImage:this.getLastImage()
	// 		},
	// 		onSuccess: (function(e){this.viewPictureImages(e)}).bind(this)
	// 	});
	// };
	// MAIN.prototype.getLastImage = function(){
	// 	var pictures = document.getElementById("pictures");
	// 	if(pictures === null){return ""}
	// 	var imgs = pictures.getElementsByTagName("img");
	// 	return (imgs.length === 0)?"":imgs[(imgs.length -1)].getAttribute("data-id");
	// };

	// MAIN.prototype.setEvent_removeImageDialog = function(){
	// 	var prop_bg = document.getElementsByClassName("ImageDialog-bg");
	// 	if(prop_bg.length > 0){
	// 		prop_bg[0].parentNode.removeChild(prop_bg[0]);
	// 	}
	// 	document.body.style.setProperty("overflow","auto","");
	// };
// 	MAIN.prototype.setEvent_picsClick = function(event){
// 		var target = event.target;
// 		if(!target){return}
// // console.log(target.className);
// 		var img;
// 		if(target.tagName === "IMG"){
// 			img = target;
// 		}
// 		else if(target.tagName === "DIV"){
// 			var imgs = target.getElementsByTagName("img");
// 			if(!imgs.length){return}
// 			img = imgs[0];
// 		}
// 		else{
// 			return;
// 		}

// 		var id  = img.getAttribute("data-id");
// 		var ext = img.getAttribute("data-ext");

// 		var mode_elm = document.getElementById("eyecatch_mode");
// 		if(mode_elm === null){return}
// 		var mode = mode_elm.value;
// 		// eyecatch
// 		if(mode === "eyecatch"){
// 			this.setEvent_selectEyecatch(id,ext);
// 		}
// 		// img-tag
// 		else if(mode === "imgTag"){
// 			this.setEvent_selectImage(id,ext);
// 		}

// 		// hidden dialog
// 		this.setEvent_removeImageDialog();

// 	};

	// MAIN.prototype.viewPictureImages = function(res){
	// 	if(!res){return}
	// 	var pictures = document.getElementById("pictures");
	// 	if(pictures !== null){
	// 		pictures.innerHTML += res;
	// 	}
	// 	this.setEvent_imagesDialogSelect();

	// 	// 選択済み画像処理
	// 	if(document.getElementById("eyecatch_file")!== null && document.getElementById("eyecatch_file").value !== ""){
	// 		var fileId   = document.getElementById("eyecatch_file").value;
	// 		var pictures = document.getElementById("pictures");
	// 		var pics = pictures.getElementsByTagName("img");
	// 		for(var i=0; i<pics.length; i++){
	// 			if(pics[i].getAttribute("data-id") === fileId){
	// 				pics[i].parentNode.parentNode.setAttribute("data-active","active");
	// 				break;
	// 			}
	// 		}
	// 	}
	// };

	MAIN.prototype.setEvent_selectEyecatch = function(filename){
// console.log(filename);return;
		var paths = this.filename2info(filename);
		if(!paths){return "";}
		var path = paths.dir + paths.file;
		// var dir  = paths.dir;
		// var file = paths.file.split(".")[0];
		// var ext  = paths.file.split(".")[1];
// console.log(paths);return;

		document.forms["form1"]["eyecatch"].value = path;
		var eyecatch_image_area = document.getElementById("eyecatch");
		var eyecatch_image      = eyecatch_image_area.getElementsByTagName("img");
		if(eyecatch_image.length > 0){
			eyecatch_image[0].src = path;
		}

		// 画像一覧を閉じる
		this.image_lists_close();
	};

	// MAIN.prototype.setEvent_selectImage = function(id,ext){
	// 	var word = "<img src='data/picture/"+id+"."+ext+"' data-id='"+id+"' alt='' />";
	// 	var textarea = document.getElementById('source');

	// 	// add-textarea
	// 	var sentence = textarea.value;//全部文字
	// 	var len      = sentence.length;//文字全体のサイズ
	// 	var pos      = textarea.selectionStart;//選択している最初の位置
	// 	var before   = sentence.substr(0, pos);
	// 	// var word     = '挿入したい文字列';
	// 	var after    = sentence.substr(pos, len);
	// 	sentence = before + word + after;
	// 	textarea.value = sentence;
	// };


	MAIN.prototype.viewPreview = function(){
		var previewButton = document.querySelector(".controls .preview");
		if(!previewButton){return}
		// console.log(previButton);
		previewButton.onclick = function(){
			var urlinfo    = new LIB().urlinfo();
			var id         = urlinfo.query.id;
			var windowName = "MYNT_blog_preview_"+id;
			// var url        = urlinfo.dir + "?plugin=blog&p=blog&b="+id+"&preview=true";
			var url        = urlinfo.dir + "?p=system&f=blog_preview&id="+id;
			window.open(url , windowName);
		};
	};

	MAIN.prototype.setRemove = function(){
		var removeButton = document.querySelector("input[value='Remove']");
		if(!removeButton){return}
		removeButton.onclick = function(){
			document.forms["form1"]["mode"].value = "remove";
			document.forms["form1"].submit();
		};
	};

	MAIN.prototype.keyboard = function(){
		var textarea = document.getElementById("source");
		if(textarea === null){return}
		textarea.onkeydown = function(event){

			//tab
			if(event.keyCode === 9){
				var textarea = document.getElementById('source');

				// add-textarea
				var sentence = textarea.value;//全部文字
				var len      = sentence.length;//文字全体のサイズ
				var pos      = textarea.selectionStart;//選択している最初の位置
				var before   = sentence.substr(0, textarea.selectionStart);
				var after    = sentence.substr(textarea.selectionEnd, len);
				var word 		 = "\t";
				sentence = before + word + after;
				textarea.value = sentence;
				textarea.setSelectionRange(len,pos+1);
				return false;
			}
		}
	};

	MAIN.prototype.setSource = function(){
		var source = document.getElementById("source");
		if(!source){return;}

		this.sourseSize();
		new LIB().event(source , "click"  , (function(e){this.sourseSize(e)}).bind(this));
		new LIB().event(source , "change" , (function(e){this.sourseSize(e)}).bind(this));
		new LIB().event(source , "keyup"  , (function(e){this.sourseSize(e)}).bind(this));
	};
	MAIN.prototype.sourseSize = function(){
		var source = document.getElementById("source");
		if(!source){return;}
		var h = source.scrollHeight;
		source.style.setProperty("height",h+"px","");
		console.log(h);
	};




	MAIN.prototype.setEvent_eyecatch = function(){
		var eyecatch_add = document.getElementById("eyecatch_add");
		if(eyecatch_add !== null){
			eyecatch_add.onclick = (function(e){this.setEvent_eyecatch_add()}).bind(this);
		}
		var eyecatch_del = document.getElementById("eyecatch_del");
		if(eyecatch_del !== null){
			eyecatch_del.onclick = (function(e){this.setEvent_eyecatch_del(e)}).bind(this);
		}
	};
	MAIN.prototype.setEvent_eyecatch_add = function(event){
		var eyecatch = document.forms["form1"]["eyecatch"];
		this.setImageButton("eyecatch",eyecatch.value);
	};
	MAIN.prototype.setEvent_eyecatch_del = function(event){
		if(!confirm("アイキャッチを削除してもよろしいですか？")){return;}

		// imgTag
		var eycatch_img = document.getElementsByClassName("eycatch_img");
		if(eycatch_img.length >= 1){
			eycatch_img[0].src = "";
		}

		// input-hidden
		var input = document.forms["form1"]["eyecatch"];
		if(input){
			input.value = "";
		}

		document.getElementById("eyecatch_add").style.setProperty("display","inline-block","");
		document.getElementById("eyecatch_del").style.setProperty("display","none","");

	};




	// ----------
	// addTags

	MAIN.prototype.setEvent_addTag = function(){
		var addTag = document.getElementsByClassName("addTag");
		for(var i=0; i<addTag.length; i++){
			addTag[i].onclick = (function(e){this.setEvent_addTag_click(e)}).bind(this);
		}
	};
	MAIN.prototype.setEvent_addTag_click = function(event){
		var target = event.target;
		if(!target){return}
		var tag = this.trim(target.textContent);
		switch(tag){
			case "img":
				this.setImageButton("imgTag","");
				break;
			case "a":
				var url = window.prompt("URLを入力してください。","");
				if(url !== null){
					this.setEvent_addTag_proc(tag+" href='"+url+"' target='_blank'",tag,"");
				}
				break;
			case "hr":
				this.setEvent_addTag_proc(tag,"","");
				break;
			case "form":
				this.setEvent_addTag_proc(tag+" method='post' action=''",tag,"\n");
				break;
			case "text":
				this.setEvent_addTag_proc("input type='text' name='' value=''","","");
				break;
			case "hidden":
				this.setEvent_addTag_proc("input type='hidden' name='' value=''","","");
				break;
			case "radio":
				this.setEvent_addTag_proc("input type='radio' name='' value=''","","");
				break;
			case "checkbox":
				this.setEvent_addTag_proc("input type='checkbox' name='' value=''","","");
				break;
			case "select":
				this.setEvent_addTag_proc(tag+" name=''",tag,"\n");
				break;
			case "option":
				this.setEvent_addTag_proc(tag+" value=''",tag,"");
				break;
			case "button":
				this.setEvent_addTag_proc("input type='button' name='' value=''","","");
				break;
			case "submit":
				this.setEvent_addTag_proc("input type='submit' name='' value=''","","");
				break;
			case "table+":
				this.setEvent_addTag_proc("table","table","\n<tr>\n<th></th>\n</tr>\n<tr>\n<td></td>\n</tr>\n\n");
				break;
			case "ul+":
				this.setEvent_addTag_proc("ul","ul","\n<li></li>\n\n");
				break;
			case "ol+":
				this.setEvent_addTag_proc("ol","ol","\n<li></li>\n\n");
				break;
			case "dl+":
				this.setEvent_addTag_proc("dl","dl","\n<dt></dt>\n<dd></dd>\n\n");
				break;
			case "code":
				this.setEvent_addTag_code_view();
				break;
			// case "table":
			// 	var str = "\n<tr>\n<td></td>\n</tr>\n";
			// 	$$.prototype.setEvent_addTag_proc(target.textContent,target.textContent,str);
			// 	break;

			// case "ul":
			// 	$$.prototype.setEvent_addTag_proc(target.textContent,target.textContent,"\n<li></li>\n");
			// 	break;

			default:
				this.setEvent_addTag_proc(tag,tag,"");
				break;
		}
	};
	MAIN.prototype.setEvent_addTag_proc = function(tag1,tag2,str1){
		if(!tag1){
			alert("tag指定がありません");
			return;
		}
		var textarea = document.getElementById('source');

		// add-textarea
		var sentence = textarea.value;//全部文字
		var len      = sentence.length;//文字全体のサイズ
		// var pos      = textarea.selectionStart;//選択している最初の位置

		var before   = sentence.substr(0, textarea.selectionStart);

		var after    = sentence.substr(textarea.selectionEnd, len);
		var str2     = sentence.substr(textarea.selectionStart , (textarea.selectionEnd - textarea.selectionStart));
		var word     = "";
		var str      = str1 + str2;

		if(tag1 && tag2){
			word = "<"+tag1+">"+str+"</"+tag2+">";
		}
		else if(tag1 && tag2 === ""){
			word = "<"+tag1+">";
		}
		sentence = before + word + after;
		textarea.value = sentence;
	};

	MAIN.prototype.setEvent_addTag_code_btn = function(){
		var btn_close = document.querySelector("#addtag_code .btn-close");
		new LIB().event(btn_close  , "click" , (function(e){this.setEvent_addTag_code_close(e)}).bind(this));
		var btn_insert = document.querySelector("#addtag_code .btn-insert");
		new LIB().event(btn_insert , "click" , (function(e){this.setEvent_addTag_code_insert(e)}).bind(this));
	};
	MAIN.prototype.setEvent_addTag_code_view = function(){
		var code = document.getElementById("addtag_code");
		if(code !== null){
			code.style.setProperty("display","block","");
		}
	};
	MAIN.prototype.setEvent_addTag_code_close = function(){
		var code = document.getElementById("addtag_code");
		if(code !== null){
			code.style.setProperty("display","none","");
		}
		var code_source = document.querySelector("#addtag_code textarea");
		if(code_source){
			code_source.value = "";
		}
	};
	MAIN.prototype.setEvent_addTag_code_insert = function(){
		var textarea = document.getElementById('source');
		// add-textarea
		var sentence = textarea.value;//全部文字
		var len      = sentence.length;//文字全体のサイズ
		var pos      = textarea.selectionStart;//選択している最初の位置
		var before   = sentence.substr(0, textarea.selectionStart);
		var after    = sentence.substr(textarea.selectionEnd, len);
		var code = document.querySelector("#addtag_code textarea").value;
		code = code.replace(/</g,"&lt;");
		code = code.replace(/>/g,"&gt;");
		sentence = before + "\n<pre>\n"+code+"\n</pre>\n" + after;
		textarea.value = sentence;
		this.setEvent_addTag_code_close();
	};


	MAIN.prototype.trim = function(txt){
		if(!txt){return txt}
		if(typeof(txt)!=="string"){txt = txt.toString()}

		//&nbsp;文字列対策
		var nbsp = String.fromCharCode(160);//&nbsp;
		if(txt!="" && txt.indexOf(nbsp)!=-1){txt = txt.split(nbsp).join(' ');}

		//改行排除
		txt = txt.replace(/\r/g,'');
		txt = txt.replace(/\n/g,'');
		txt = txt.replace(/^\t/g,'');
		txt = txt.replace(/\t$/g,'');

		//文頭、文末のTRIM
		txt = txt.replace(/^ /g,'');
		txt = txt.replace(/ $/g,'');

		return txt;
	};

	var $$pos = function(e,t){
		//エレメント確認処理
		if(!e){return;}

		//途中指定のエレメントチェック（指定がない場合はbody）
		if(typeof(t)=='undefined' || t==null){
			t = document.body;
		}

		//デフォルト座標
		var pos={x:0,y:0};
		do{
			//指定エレメントでストップする。
			if(e == t){break}

			//対象エレメントが存在しない場合はその辞典で終了
			if(typeof(e)=='undefined' || e==null){return pos;}

			//座標を足し込む
			pos.x += e.offsetLeft;
			pos.y += e.offsetTop;
		}

		//上位エレメントを参照する
		while(e = e.offsetParent);

		//最終座標を返す
		return pos;
	};

	MAIN.prototype.setDialogWindowSize = function(){
		var pictures = document.getElementById("pictures");
		if(pictures===null){return;}
		var window_size = window.innerHeight;
		var pictures_pos = $$pos(pictures);
		pictures.style.setProperty("height", (window_size - pictures_pos.y - 20)+"px", "");
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
        // {
        //   text:"削除",
        //   click : (function(filename){
        //     if(!confirm("このファイルをサーバーから削除してもよろしいですか？この操作は取り消せません。")){return;}
				// 		$$upload.prototype.remove_image(filename);
        //   }).bind(this,filename)
				// },
				
        {
					mode:"close",
          text:"元のサイズで登録",
          click : (function(filename,e){
						var tag = this.getImageTag_original(filename);
						var textarea = document.getElementById('source');
						var sentence = textarea.value;//全部文字
						var len      = sentence.length;//文字全体のサイズ
						var pos      = textarea.selectionStart;//選択している最初の位置
						var before   = sentence.substr(0, pos);
						var after    = sentence.substr(pos, len);
						sentence = before + tag + after;
						textarea.value = sentence;
						this.image_lists_close();
          }).bind(this,filename)
				},
				
				{
					mode:"close",
          text:"600px",
          click : (function(filename,e){
						var tag = this.getImageTag_size(filename , 600);
						var textarea = document.getElementById('source');
						var sentence = textarea.value;//全部文字
						var len      = sentence.length;//文字全体のサイズ
						var pos      = textarea.selectionStart;//選択している最初の位置
						var before   = sentence.substr(0, pos);
						var after    = sentence.substr(pos, len);
						sentence = before + tag + after;
						textarea.value = sentence;
						this.image_lists_close();
          }).bind(this,filename)
				},
				
				{
					mode:"close",
          text:"300px",
          click : (function(filename,e){
						var tag = this.getImageTag_size(filename , 300);
						var textarea = document.getElementById('source');
						var sentence = textarea.value;//全部文字
						var len      = sentence.length;//文字全体のサイズ
						var pos      = textarea.selectionStart;//選択している最初の位置
						var before   = sentence.substr(0, pos);
						var after    = sentence.substr(pos, len);
						sentence = before + tag + after;
						textarea.value = sentence;
						this.image_lists_close();
          }).bind(this,filename)
        }
      ],
      // クリック挙動 [ "close" , "none" ]
      bgClick : "close",

      loaded : function(){
        // console.log("loaded!!");
      }
    });
	};
	


	MAIN.prototype.getImageTag_original = function(filename){
		var paths = this.filename2info(filename);
		if(!paths){return "";}
		var path = paths.dir + paths.file;
		var html = "<img src='"+ path +"'>";
		return html;
	};
	MAIN.prototype.getImageTag_size = function(filename , size){
		var paths = this.filename2info(filename);
		if(!paths){return "";}
		var path = $$upload.prototype.setTemplatePath_encode(paths.dir + paths.file , size);
		var html = "<img src='"+ path +"' width='"+size+"'>";
		return html;
	};

  MAIN.prototype.modal_html = function(filename){
		var paths = this.filename2info(filename);
		if(!paths){return "";}
    var html = "<img src='"+ paths.dir + paths.file +"'>";
    return html;
	};
	MAIN.prototype.filename2info = function(filename){
		if(!filename){return;}
    var img = document.querySelector(".files img[data-id='"+filename+"']");
		if(!img){return;}
		var dir  = img.getAttribute("data-dir");
		var file = img.getAttribute("data-file");
		return {
			dir  : dir,
			file : file
		};
	};
	


	// new $$();
	new LIB().construct();
})();
