(function(){

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
  
  // svgLoad
  function MAIN(){
    this.changeIMG2SVG();
    this.setSvgTag();
  };

  // ページ内のIMGタグをsvgタグに変更
  MAIN.prototype.changeIMG2SVG = function(){
    var imgTags = document.getElementsByTagName("img");
    for(var i=0; i<imgTags.length; i++){
      if(!imgTags[i].src){continue;}
      if(!imgTags[i].getAttribute("data-svg")){continue;}
      // svgファイル確認
      var pathSplit = imgTags[i].src.split("?")[0].split("/");
      var filename  = pathSplit[pathSplit.length -1];
      if(!filename){continue;}
      var extensions = filename.split(".");
      if(!extensions.length){continue;}
      var extension  = extensions[extensions.length-1];
      if(!extension || extension.toLowerCase() !== "svg"){continue;}
      new $$ajax({
        url    : imgTags[i].src,
        method : "get",
        type   : "text/plane",
        requestHeader : false,
        data   : {
          target : imgTags[i]
        },
        onSuccess : function(svgTag){
          if(!svgTag){return;}
          var div = document.createElement("div");
          div.innerHTML = svgTag;
          var elements = div.getElementsByTagName("svg");
          if(!elements.length){return;}
          var svgElement = elements[0];
          var styles = svgElement.getElementsByTagName("style");
          for(var i=styles.length-1; i>=0; i--){
            styles[i].parentNode.removeChild(styles[i]);
          }
          var svg = document.createElementNS("http://www.w3.org/2000/svg" , "svg");
          if(svgElement.getAttribute("viewBox")){
            svg.setAttribute("viewBox" , svgElement.getAttribute("viewBox"));
          }
          svg.innerHTML = svgElement.innerHTML;
          this.data.target.parentNode.insertBefore(svg , this.data.target);
          this.data.target.parentNode.removeChild(this.data.target);
        }
      });
    }
  };

  // 指定SVGタグに情報追記
  MAIN.prototype.setSvgTag = function(){
    var svgFiles = document.getElementsByTagName("svg");
    for(var i=0; i<svgFiles.length; i++){
      if(!svgFiles[i].getAttribute("src")){continue;}
      new $$ajax({
        url : svgFiles[i].getAttribute("src"),
        method : "get",
        type : "text/plane" ,
        requestHeader : false,
        data : {
          target : svgFiles[i]
        },
        onSuccess : function(svgTag){
          if(!svgTag){return;}
          var div = document.createElement("div");
          div.innerHTML = svgTag;
          var elements = div.getElementsByTagName("svg");
          if(!elements.length){return;}
          var svgElement = elements[0];
          var styles = svgElement.getElementsByTagName("style");
          for(var i=styles.length-1; i>=0; i--){
            styles[i].parentNode.removeChild(styles[i]);
          }
          var svg = this.data.target;
          if(svgElement.getAttribute("viewBox")){
            svg.setAttribute("viewBox" , svgElement.getAttribute("viewBox"));
          }
          svg.innerHTML = svgElement.innerHTML;
          svg.removeAttribute("src");
        }
      });
    }
  };

  let $$ajax = function(options){
    if(!options){return}
    // var ajax = new $$ajax;
    var httpoj = this.createHttpRequest();
    if(!httpoj){return "a";}

    // open メソッド;
    var option = this.setOption(options);

    // 実行
    httpoj.open( option.method , option.url , option.async );

    // requestHeader(open後に実行)
    if(option.requestHeader){
      httpoj.setRequestHeader('Content-Type', option.contentText);
      
    }

    // header登録
    for(var i in option.header){
      console.log(i +"="+ option.header[i]);
      httpoj.setRequestHeader(i , option.header[i]);
    }

    // onload-check
    httpoj.onreadystatechange = function(){
      //readyState値は4で受信完了;
      if (httpoj.readyState==4){
        //コールバック
        if (httpoj.status === 200) {
          option.onSuccess(httpoj.responseText);
        }
        else{
          option.onError(httpoj.responseText);
        }
      }
    };

    // responseType
    if(typeof option.responseType !== "undefined" && option.responseType){
      httpoj.responseType = option.responseType;
    }

    //query整形
    var data = this.setQuery(option);
// console.log(data);

    //send メソッド（クエリ有り）
    if(data.length){
      httpoj.send(data.join("&"));
    }
    // クエリ無し
    else{
      httpoj.send();
    }
    return "hoge";
  };
  
  $$ajax.prototype.dataOption = {
    url:"",
    query:{},				// same-key Nothing
    querys:[],			// same-key OK
    data:{},				// ETC-data event受渡用
    header:{},
    async:true,		// [true:非同期 false:同期]
    method:"POST",	// [POST / GET]
    responseType:"", // 
    // requestHeader : "application/json", // ["application/x-www-form-urlencoded"]
    requestHeader : true,
    contentText : "application/x-www-form-urlencoded",
    // requestHeader : "application/x-www-form-urlencoded", // [""]
    // type:"", // [text/javascript]...
    onSuccess:function(res){},
    onError:function(res){}
  };
  $$ajax.prototype.option = {};
  $$ajax.prototype.createHttpRequest = function(){
    //Win ie用
    if(window.ActiveXObject){
      //MSXML2以降用;
      try{return new ActiveXObject("Msxml2.XMLHTTP")}
      catch(e){
        //旧MSXML用;
        try{return new ActiveXObject("Microsoft.XMLHTTP")}
        catch(e2){return null}
      }
    }
    //Win ie以外のXMLHttpRequestオブジェクト実装ブラウザ用;
    else if(window.XMLHttpRequest){return new XMLHttpRequest()}
    else{return null}
  };
  $$ajax.prototype.setOption = function(options){
    var option = {};
    for(var i in this.dataOption){
      if(typeof options[i] != "undefined"){
        option[i] = options[i];
      }
      else{
        option[i] = this.dataOption[i];
      }
    }
    return option;
  };
  $$ajax.prototype.setQuery = function(option){
    var data = [];
    if(typeof option.query != "undefined"){
      for(var i in option.query){
        data.push(i+"="+encodeURIComponent(option.query[i]));
      }
    }
    if(typeof option.querys != "undefined"){
      for(var i=0;i<option.querys.length;i++){
        if(typeof option.querys[i] == "Array"){
          data.push(option.querys[i][0]+"="+encodeURIComponent(option.querys[i][1]));
        }
        else{
          var sp = option.querys[i].split("=");
          data.push(sp[0]+"="+encodeURIComponent(sp[1]));
        }
      }
    }
    return data;
  };


  switch(document.readyState){
    case "complete" : new MAIN();break;
    default : window.addEventListener("load" , function(){new MAIN()});break;
  }
})();