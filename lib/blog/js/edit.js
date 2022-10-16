;(function(){
	let datas = {
		times : [
			"00:00","00:30",
			"01:00","01:30",
			"02:00","02:30",
			"03:00","03:30",
			"04:00","04:30",
			"05:00","05:30",

			"06:00","06:30",
			"07:00","07:30",
			"08:00","08:30",
			"09:00","09:30",
			"10:00","10:30",
			"11:00","11:30",

			"12:00","12:30",
			"13:00","13:30",
			"14:00","14:30",
			"15:00","15:30",
			"16:00","16:30",
			"17:00","17:30",

			"18:00","18:30",
			"19:00","19:30",
			"20:00","20:30",
			"21:00","21:30",
			"22:00","22:30",
			"23:00","23:30"
		]
		// times : [
		// 	{value:"00:00"},{value:"00:30"},
		// 	{value:"01:00"},{value:"01:30"},
		// 	{value:"02:00"},{value:"02:30"},
		// 	{value:"03:00"},{value:"03:30"},
		// 	{value:"04:00"},{value:"04:30"},
		// 	{value:"05:00"},{value:"05:30"},

		// 	{value:"06:00"},{value:"06:30"},
		// 	{value:"07:00"},{value:"07:30"},
		// 	{value:"08:00"},{value:"08:30"},
		// 	{value:"09:00"},{value:"09:30"},
		// 	{value:"10:00"},{value:"10:30"},
		// 	{value:"11:00"},{value:"11:30"},

		// 	{value:"12:00"},{value:"12:30"},
		// 	{value:"13:00"},{value:"13:30"},
		// 	{value:"14:00"},{value:"14:30"},
		// 	{value:"15:00"},{value:"15:30"},
		// 	{value:"16:00"},{value:"16:30"},
		// 	{value:"17:00"},{value:"17:30"},

		// 	{value:"18:00"},{value:"18:30"},
		// 	{value:"19:00"},{value:"19:30"},
		// 	{value:"20:00"},{value:"20:30"},
		// 	{value:"21:00"},{value:"21:30"},
		// 	{value:"22:00"},{value:"22:30"},
		// 	{value:"23:00"},{value:"23:30"}
		// ]
	};


	var MAIN = function(){
    this.set_event();
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

		// アイキャッチ画像表示
		this.view_eyecatch_image();

		// アイキャッチ[追加/削除]ボタン表示切替
		this.toggle_eyecatch_button();

		// set-value
		this.set_schedule_start();

		// schedule-event-set
		this.set_calendar();
		this.set_time();

		

		// group
		this.set_group();

		// tag-regist
		this.set_tag_regist();

		// text-count
		this.set_text_count();
	};
	
	// アイキャッチ画像表示
	MAIN.prototype.view_eyecatch_image = function(){
		let path = document.forms.form1.eyecatch.value;
		if(!path){return;}
		document.querySelector("#eyecatch > img.eyecatch_img").setAttribute("src" , path);
	};
	// アイキャッチ[追加/削除]ボタン表示切替
	MAIN.prototype.toggle_eyecatch_button = function(){
		if(document.querySelector("#eyecatch > img.eyecatch_img").getAttribute("src")){
			document.getElementById("eyecatch_add").style.setProperty("display","none","");
			document.getElementById("eyecatch_del").style.setProperty("display","inline-block","");
		}
		else{
			document.getElementById("eyecatch_add").style.setProperty("display","inline-block","");
			document.getElementById("eyecatch_del").style.setProperty("display","none","");
		}
	};
  
  MAIN.prototype.set_event = function(){
    let elm_info_property = document.querySelector(".info-property .button a");
    if(elm_info_property){
      new $$lib().event(elm_info_property , "click" , (function(e){this.view_info_property(e)}).bind(this));
		}
		let elm_back = document.querySelector(".back");
		if(elm_back){
			new $$lib().event(elm_back , "click" , (function(e){this.click_back(e)}).bind(this));
		}
		let tag_pulldown_buttons = document.querySelectorAll(".tag-control-area > ul > li[data-mode='pulldown']");
		if(tag_pulldown_buttons){
			for(let i=0; i<tag_pulldown_buttons.length; i++){
				new $$lib().event(tag_pulldown_buttons[i] , "click" , (function(e){this.toggle_tag_edit_button(e)}).bind(this));
			}
		}
  };

  MAIN.prototype.view_info_property = function(e){
    let elm_info_property = document.querySelector(".info-property");
    if(!elm_info_property){return;}
    if(elm_info_property.getAttribute("data-toggle") !== "1"){
      elm_info_property.setAttribute("data-toggle" , "1");
    }
    else{
      elm_info_property.setAttribute("data-toggle" , "0");
    }
	};
	MAIN.prototype.click_back = function(e){
		let urlinfo = new $$lib().urlinfo();
		urlinfo.query.c = "blog/edit_lists";
		let querys = [];
		for(let i in urlinfo.query){
			querys.push(i+"="+urlinfo.query[i]);
		}
		let url = urlinfo.url +"?"+ querys.join("&");
		location.href = url;

		// // edirect
		// let redirect = document.forms.form1.redirect.value = url;
	};

	MAIN.prototype.changeSelect = function(event){
		var target = event.target;
		var urlData = new $$lib().urlinfo();
		var url = urlData.url+"?b="+urlData.query.b+"&p="+urlData.query.p+"&file="+target.value;
		location.href = url;
	};
	MAIN.prototype.toggle_tag_edit_button = function(e){
		let target = e.currentTarget;
		if(target.getAttribute("data-active") !== "1"){
			target.setAttribute("data-active" , "1");
		}
		else{
			target.setAttribute("data-active" , "0");
		}
		// other-active-clear
		let pulldowns = document.querySelectorAll(".tag-control-area > ul > li[data-mode='pulldown']");
// console.log(pulldowns.length);
		for(let i=0; i<pulldowns.length; i++){
			if(pulldowns[i] === target){continue;}
			if(pulldowns[i].getAttribute("data-active") !== "1"){continue;}
			pulldowns[i].setAttribute("data-active" , "0");
		}
	};

	// MAIN.prototype.urlinfo=function(uri){
	// 	if(!uri){uri = location.href;}
	// 	var data={};
	// 	//URLとクエリ分離分解;
	// 	var query=[];
	// 	if(uri.indexOf("?")!=-1){query = uri.split("?")}
	// 	else if(uri.indexOf(";")!=-1){query = uri.split(";")}
	// 	else{
	// 		query[0] = uri;
	// 		query[1] = '';
	// 	}
	// 	//基本情報取得;
	// 	var sp = query[0].split("/");
	// 	var data={
	// 		url:query[0],
	// 		dir:this.pathinfo(uri).dirname,
	// 		domain:sp[2],
	// 		protocol:sp[0].replace(":",""),
	// 		query:(query[1])?(function(q){
	// 			var data=[];
	// 			var sp = q.split("&");
	// 			for(var i=0;i<sp .length;i++){
	// 				var kv = sp[i].split("=");
	// 				if(!kv[0]){continue}
	// 				data[kv[0]]=kv[1];
	// 			}
	// 			return data;
	// 		})(query[1]):[],
	// 	};
	// 	return data;
	// };
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


	MAIN.prototype.setImageButton = function(mode , selectedImage){
		selectedImage = selectedImage ? selectedImage : "";
		// let page = document.getElementById("page").value;
		var tempfile = "lib/blog/html/upload_modal.html";
		new $$ajax({
			url: location.href,
			method:"POST",
			async:true,
			query:{
				php    : '\\lib\\html\\file::getFile("'+ tempfile +'")',
				mode   : mode,
				exit   : true
			},
			onSuccess:(function(mode , source){
				this.setImageDialog_temp(mode , source);
			}).bind(this , mode)
		});
	};

	MAIN.prototype.setImageDialog_temp = function(mode , article){

		// dialog-view
		var bg = document.createElement("div");
		bg.className = "ImageDialog-bg";
		bg.style.setProperty("top" , window.pageYOffset + "px" , "");
		bg.innerHTML = article;
		document.body.appendChild(bg);

		// 削除ボタン
		var button_close = document.querySelector(".image-pickup-dialog-close");
		if(button_close){
			new $$lib().event(button_close , "click" , (function(e){this.image_lists_close(e)}).bind(this));
		}

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
		this.upload_image = new $$upload({
			mode : mode,
			image_click : (function(filename){
				var mode = this.upload_image.options.mode;
				switch(mode){
					case "imgTag":
						this.modal_image_info(filename);
						break;
					case "eyecatch":
						this.setEvent_selectEyecatch(filename);
						break;
				}
			}).bind(this),
			file_click : (function(data){
				// console.log(data);
				let urlinfo = new $$lib().urlinfo();
				let url = urlinfo.dir + data.dir+data.file;
				this.setEvent_addTag_proc('a href="'+url+'" target="_blank"' , "a" , url);
				this.image_lists_close();
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
		var fl = new $$lib().urlinfo(location.href);
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
	};


	MAIN.prototype.setEvent_selectEyecatch = function(filename){console.log(filename);
		var paths = this.filename2info(filename);
		if(!paths){return "";}
		var path = paths.dir + paths.file;

		document.forms["form1"]["eyecatch"].value = path;
		var eyecatch_image_area = document.getElementById("eyecatch");
		var eyecatch_image      = eyecatch_image_area.getElementsByTagName("img");
		if(eyecatch_image.length > 0){
			eyecatch_image[0].src = path;
		}

		// 画像一覧を閉じる
		this.image_lists_close();

		// アイキャッチ[追加/削除]ボタン表示切替
		this.toggle_eyecatch_button();
	};

	
	MAIN.prototype.viewPreview = function(){
		var previewButton = document.querySelector(".preview-link");
		if(!previewButton){return}
		let urlinfo = new $$lib().urlinfo();
		let id      = urlinfo.query.id;
		let type    = urlinfo.query.type;
		if(!id || !type){return;}
		let querys = {b : id};
		if(type && Number(type) > 2){
			querys.type = type;
		}
		let url     = urlinfo.url +"?"+ this.make_query(querys);
// http://localhost/myntpage/?q=Yz1ibG9nJmlkPTM=
// http://localhost/myntpage/?c=blog&id=3
		previewButton.textContent = url;
		previewButton.setAttribute("data-window-name" , "MYNT_blog_preview_"+ type +"_"+ id);
		previewButton.onclick = (function(e){
			let url  = e.target.textContent;
			var name = e.target.getAttribute("data-window-name");
			window.open(url , name);
		}).bind(this);
	};
	MAIN.prototype.make_query = function(querys){
		let arr = [];
		for(let i in querys){
			arr.push(i+"="+querys[i]);
		}
		let query = arr.join("&");
		return query;
	};
	MAIN.prototype.make_query_base64 = function(querys){
		let query = this.make_query(querys);
		return "q="+btoa(query);
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
		var textarea = document.getElementById("article");
		if(textarea === null){return}
		textarea.onkeydown = function(event){

			//tab
			if(event.keyCode === 9){
				var textarea = document.getElementById('article');

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
		var article = document.getElementById("article");
		if(!article){return;}

		this.sourseSize();
		new $$lib().event(article , "click"  , (function(e){this.sourseSize(e)}).bind(this));
		new $$lib().event(article , "change" , (function(e){this.sourseSize(e)}).bind(this));
		new $$lib().event(article , "keyup"  , (function(e){this.sourseSize(e)}).bind(this));
	};
	MAIN.prototype.sourseSize = function(){
		var article = document.getElementById("article");
		if(!article){return;}
		var h = article.scrollHeight;
		article.style.setProperty("height",h+"px","");
		console.log(h);
	};




	MAIN.prototype.setEvent_eyecatch = function(){
		var eyecatch_add = document.getElementById("eyecatch_add");
		if(eyecatch_add !== null){
			// eyecatch_add.onclick = (function(e){this.setEvent_eyecatch_add()}).bind(this);
			new $$lib().event(eyecatch_add , "click" , (function(e){this.setEvent_eyecatch_add()}).bind(this));
		}
		var eyecatch_del = document.getElementById("eyecatch_del");
		if(eyecatch_del !== null){
			// eyecatch_del.onclick = (function(e){this.setEvent_eyecatch_del(e)}).bind(this);
			new $$lib().event(eyecatch_del , "click" , (function(e){this.setEvent_eyecatch_del(e)}).bind(this));
		}
	};
	MAIN.prototype.setEvent_eyecatch_add = function(event){
		var eyecatch = document.forms.form1.eyecatch;
		this.setImageButton("eyecatch" , eyecatch.value);
	};
	MAIN.prototype.setEvent_eyecatch_del = function(event){
		if(!confirm("アイキャッチを削除してもよろしいですか？")){return;}

		// imgTag
		var eyecatch_img = document.getElementsByClassName("eyecatch_img");
		if(eyecatch_img.length >= 1){
			eyecatch_img[0].src = "";
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
		let target2 = new $$lib().upperSelector(target , "button");
		let tag = target2.getAttribute("data-tag");
		switch(tag){
			case "img":
				this.setImageButton("imgTag","");
				break;
			case "a":
				this.setEvent_addTag_a();
				break;
			case "hr":
				this.setEvent_addTag_proc(tag,"","");
				break;
			case "form":
				this.setEvent_addTag_proc(tag+" method='post' action=''",tag,"\n");
				break;
			case "input-text":
				this.setEvent_addTag_proc("input type='text' name='' value=''","","");
				break;
			case "input-hidden":
				this.setEvent_addTag_proc("input type='hidden' name='' value=''","","");
				break;
			case "input-radio":
				this.setEvent_addTag_proc("input type='radio' name='' value=''","","");
				break;
			case "input-checkbox":
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
			case "input-submit":
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

	MAIN.prototype.get_selectValue = function(){
		var textarea  = document.getElementById('article');
		if(!textarea){return;}
		var sentence  = textarea.value;//全部文字
		var pos_start = textarea.selectionStart;//選択している最初の位置
		let pos_end   = textarea.selectionEnd;
		return sentence.substr(pos_start , pos_end - pos_start);

	};

	MAIN.prototype.setEvent_addTag_a = function(){
		let str = this.get_selectValue();
		let url = str.match(/^(http:\/\/|https:\/\/)/) ? str : "";
		let html = "";
		html += "<div class='modal-set-url'>";
		html += "<p>url</p>";
		html += "<input type='text' name='url' value='"+ url +"' placeholder=''>";
		html += "<p>文字列</p>";
		html += "<input type='text' name='str' value='"+ str +"' placeholder=''>";
		html += "</div>";
		new $$modal({
      size    : {
        width : "500px",
        height: "auto"
      },
      position : {
        vertical : "top",
        horizon  : "center",
        margin   : ["100px","10px","10px","10px"]
      },
      title   : "Title",
      message : {
        html   : html,
        height : "auto",
        align  : "center"
      },
      button  : [
				{
					mode:"close",
          text:"登録",
          click : (function(e){
						let url = document.querySelector(".modal-area input[name='url']").value;
						let str = document.querySelector(".modal-area input[name='str']").value;
						let tag = '<a href="'+ url +'" target="_blank">'+ str +'</a>';
						var textarea  = document.getElementById('article');
						var sentence  = textarea.value;
						var len       = sentence.length;
						var pos_start = textarea.selectionStart;
						let pos_end   = textarea.selectionEnd;
						var before    = sentence.substr(0, pos_start);
						var after     = sentence.substr(pos_end, len);
						sentence = before + tag + after;
						textarea.value = sentence;
          }).bind(this)
				}
      ],
      loaded : function(){},
			bgClick : false
    });
	};

	MAIN.prototype.setEvent_addTag_proc = function(tag1,tag2,str1){
		if(!tag1){
			alert("tag指定がありません");
			return;
		}
		var textarea = document.getElementById('article');

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
    if(btn_close){
      new $$lib().event(btn_close  , "click" , (function(e){this.setEvent_addTag_code_close(e)}).bind(this));
    }
    var btn_insert = document.querySelector("#addtag_code .btn-insert");
    if(btn_insert){
      new $$lib().event(btn_insert , "click" , (function(e){this.setEvent_addTag_code_insert(e)}).bind(this));
    }
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
		var textarea = document.getElementById('article');
		// add-textarea
		var sentence = textarea.value;//全部文字
		var len      = sentence.length;//文字全体のサイズ
		var pos      = textarea.selectionStart;//選択している最初の位置
		var before   = sentence.substr(0, textarea.selectionStart);
		var after    = sentence.substr(textarea.selectionEnd, len);
		var code = document.querySelector("#addtag_code textarea").value;
		code = code.replace(/</g,"&lt;");
		code = code.replace(/>/g,"&gt;");
		sentence = before + "<code class='mynt-code'>"+code+"</code>" + after;
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
						var textarea = document.getElementById('article');
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
						var textarea = document.getElementById('article');
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
						var textarea = document.getElementById('article');
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

	MAIN.prototype.set_schedule_start = function(){
		let elm_schedule = document.forms.form1.schedule;
		let datetime = elm_schedule.value;
		if(!datetime){
			this.set_schedule();
			return;
		}
		let y = datetime.substr(0,4);
		let m = datetime.substr(4,2);
		let d = datetime.substr(6,2);
		let h = datetime.substr(8,2);
		let i = datetime.substr(10,2);
		let s = datetime.substr(12,2);
		let elm_date     = document.forms.form1.date_input;
		let elm_time     = document.forms.form1.time_input;
		elm_date.value = y+"/"+m+"/"+d;
		elm_time.value = h+":"+i;
	};
	MAIN.prototype.set_schedule = function(){
		let elm_schedule = document.forms.form1.schedule;
		let elm_date     = document.forms.form1.date_input;
		let elm_time     = document.forms.form1.time_input;
		let date = elm_date.value.replace(/\//g,"");
		let time = elm_time.value.replace(/\:/g,"") +"00";
		elm_schedule.value = String(date) + String(time);
	};
	MAIN.prototype.set_calendar = function(){
    new $$calendars({
      target : "input[name='date_input']",
      start_event : "focus",
      date_type     : "today",
      date_string   : null,
      format_output : "yyyy/mm/dd",
      flg_date_active : "all",
      select : (function(ymd){
				// console.log(ymd);
				this.set_schedule();
      }).bind(this)
		});
  };
	

	
	MAIN.prototype.set_time = function(){
		let elm = document.querySelector("input[name='time_input']");
		if(!elm.value){
			let dt = new Date();
			let h = ("00" + dt.getHours()).slice(-2);
			let m = ("00" + dt.getMinutes()).slice(-2);
			elm.value = h+ ":" + m;
		}
		let times = this.get_times(elm.value);
    new $$pullDown({
			datas    : times,  // ex)[{key:value},{key:value},{key:value},...]
			input_match : "full", // ["partial":部分一致 , "forward":前方一致 , "full"]
			brank_view  : true,      // [true:ブランクで表示 , false:文字入力で表示]
			readonly    : false,     // 対象項目を全てreadonlyにセットする
			listonly    : false ,    // リストに無い項目は登録不可
			all_view    : true ,     // リストを常に全部表示
			multiple    : false ,    // 複数選択モード
			elements : [    // ex) elm_val(value)->表示,elm_key(key,id)->非表示
				{
					// elm_key : "[name='aaa_key']", // value値を登録するelement※任意
					elm_val : "input[name='time_input']"  // key(id)値を登録するelement※任意（key値は無くても可） 
				} 
			],
			attach   : (function(e,a){
// console.log(e.target.value);//入力値取得
				// this.get_times(e.target.value);
			}).bind(this),
			selected : (function(e){
				this.set_schedule();
			}).bind(this)
		});

		this.set_schedule();
		elm.addEventListener("change" , (function(){
			this.set_schedule();
		}).bind(this));
	};
	MAIN.prototype.get_times = function(current_time){
		let times = datas.times;

		// current-timeがリストに含まれていない場合は、適所に追加する。
		if(current_time && times.indexOf(current_time) === -1){
			let current_num = Number(current_time.replace(":",""));
			let flg = false;
			for(let i=0; i<times.length; i++){
				let time_num = Number(times[i].replace(":",""));
				if(time_num < current_num){continue;}
				times.splice(i , 0 , current_time);
				flg = true;
				break;
			}
			if(!flg){
				times.push(current_time);
			}
		}
		datas.times = times;
		// pulldown-lib形式に整形
		let res_times = [];
		for(let i=0; i<times.length; i++){
			res_times[i] = {value:times[i]};
		}
		return res_times;
	};

	MAIN.prototype.set_group = function(){
		let page = document.getElementById("page").value;
		let urlinfo = new $$lib().urlinfo();
		let type = urlinfo.query.type || 1;
		new $$ajax({
			url : location.href,
			query : {
				php  : '\\lib\\blog\\php\\group::load_json("'+type+'")',
				exit : true
			},
			onSuccess : (function(res){
				if(!res){return;}
				let json = JSON.parse(res);
				let datas = [];
				if(json.length){
					for(let i=0; i<json.length; i++){
						datas.push({
							key   : json[i].id,
							value : json[i].name
						});
					}
				}
				let elm_key   = document.querySelector("[name='group']");
				let elm_value = document.querySelector("[name='group_name']");
				if(elm_key && elm_value && elm_key.value !== ""){
					for(let i=0; i<json.length; i++){
						if(json[i].id == elm_key.value){
							elm_value.value = json[i].name;
							break;
						}
					}
				}

				new $$pullDown({
					datas       : datas,  // ex)[{key:value},{key:value},{key:value},...]
					input_match : "partial", // ["partial":部分一致 , "forward":前方一致]
					brank_view  : true,      // [true:ブランクで表示 , false:文字入力で表示]
					readonly    : false,     // 対象項目を全てreadonlyにセットする
					listonly    : false ,    // リストに無い項目は登録不可
					all_view    : false ,     // リストを常に全部表示
					multiple    : false ,     // 複数選択モード
					elements : [    // ex) elm_val(value)->表示,elm_key(key,id)->非表示
						{
							elm_key : "[name='group']", // value値を登録するelement※任意
							elm_val : "[name='group_name']"  // key(id)値を登録するelement※任意（key値は無くても可） 
						} 
					]
				});
			}).bind(this)
		});
		
	};
	
	MAIN.prototype.set_tag_regist = function(){
		new $$tag_regist({
			target_area : ".tag-area",
			target_form : "input[name='tag']",
			form_mode   : "json",
			tag_css : {
				"background-color" : "#88c",
				"border-radius"    : "4px"
			},
			add : (function(word){console.log("add : "+ word);}).bind(this),
			del : (function(word){console.log("del : "+ word);}).bind(this)
		});
	};

	MAIN.prototype.set_text_count = function(){
		let textarea = document.getElementById("article");
		if(!textarea){return;}
		new $$lib().event(textarea , "input" , this.check_text_count.bind(this));
		this.check_text_count();
	};
	MAIN.prototype.check_text_count = function(){
		let count_view_elm = document.querySelector(".text-counts");
		if(!count_view_elm){return;}
		let textarea = document.getElementById("article");
		if(!textarea){return;}
		count_view_elm.textContent = textarea.value.length;
	};


	// new $$();
	new $$lib().construct(MAIN);
})();
