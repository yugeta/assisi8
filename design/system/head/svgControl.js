;$$SVG = (function(){
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
  
  // svgLoad
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
    this.changeIMG2SVG();
    this.setSvgTag();
  };

  // ページ内のIMGタグをsvgタグに変更
  $$.prototype.changeIMG2SVG = function(){
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
      new $$MYNT_AJAX({
        url : imgTags[i].src,
        method : "get",
        type : "text/plane" ,
        data : {
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
  $$.prototype.setSvgTag = function(){
    var svgFiles = document.getElementsByTagName("svg");
    for(var i=0; i<svgFiles.length; i++){
      if(!svgFiles[i].getAttribute("src")){continue;}
// console.log(svgFiles[i].getAttribute("src"));
      new $$MYNT_AJAX({
        url : svgFiles[i].getAttribute("src"),
        method : "get",
        type : "text/plane" ,
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

  new $$;
  return $$;
})();