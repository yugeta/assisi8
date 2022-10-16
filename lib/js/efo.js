/**
 * EFO
 * ## how-to
 * 必須項目チェック : requiredのセットされているinputタグの未入力に赤色のbg-colorをセットする。（入力したら色を無くす）
 */

;$$EFO = (function(){

  var $$options = {
    checkEvent   : function(){},  // 任意の処理を実行できる
    submitButton : []  // 送信ボタン処理様（エレメントを複数記述できる）
  };

  // ページ起動判定処理
  var $$ = function(options){

    for(var i in options){
      $$options[i] = options[i];
    }

    var state = document.readyState;
		if(state === "complete"){
			this.start();
		}
		else if(state === "interactive"){
			$$event(window , "DOMContentLoaded" , this.start);
		}
		else{
			$$event(window , "load" , this.start);
		}
  };

  // ページ読み込み後の実行処理
  $$.prototype.start = function(){
    $$.prototype.efo.setCSS();
    $$.prototype.efo.setForm();
    $$.prototype.efo.checkForm();
    $$.prototype.efo.setValueClear();
    // $$options
    $$options.checkEvent();
  };

  $$.prototype.efo = {
    valueCSS : ""
    + "input[data-efo-empty='1']{background-color:#FEE;}"
    // + "input[data-efo-valueClear='1']{"
    // + "position : relative;"
    // + "}"
    // + "input[data-efo-valueClear='1']:before{"
    // + "content : ' ';"
    // + "/*content  : url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjwhRE9DVFlQRSBzdmcgIFBVQkxJQyAnLS8vVzNDLy9EVEQgU1ZHIDEuMC8vRU4nICAnaHR0cDovL3d3dy53My5vcmcvVFIvMjAwMS9SRUMtU1ZHLTIwMDEwOTA0L0RURC9zdmcxMC5kdGQnPjxzdmcgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMjQgMjQiIGlkPSJMYXllcl8xIiB2ZXJzaW9uPSIxLjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+PGc+PHBhdGggZD0iTTEyLDJDNi41LDIsMiw2LjUsMiwxMmMwLDUuNSw0LjUsMTAsMTAsMTBzMTAtNC41LDEwLTEwQzIyLDYuNSwxNy41LDIsMTIsMnogTTE2LjksMTUuNWwtMS40LDEuNEwxMiwxMy40bC0zLjUsMy41ICAgbC0xLjQtMS40bDMuNS0zLjVMNy4xLDguNWwxLjQtMS40bDMuNSwzLjVsMy41LTMuNWwxLjQsMS40TDEzLjQsMTJMMTYuOSwxNS41eiIvPjwvZz48L3N2Zz4=');*/"
    // + "position : absolute;"
    // + "top : 50%;"
    // + "right : 8px;"
    // + "width : 20px;"
    // + "height : 20px;"
    // + "background-color:red;"
    // + "}"
    + "",
    
    setCSS : function(){
      var head = document.getElementsByTagName("head");
      if(head.length){
        var css = $$.prototype.efo.valueCSS;
        var style  = document.createElement("style");
        style.type = "text/css";
        style.innerHTML = css;
        head[0].appendChild(style);
      }
    },
    setForm : function(){
      // forms
      for(var i=0; i<document.forms.length; i++){
        // tags
        for(var j=0; j<document.forms[i].length; j++){
          $$event(document.forms[i][j] , "focus"  , $$.prototype.efo.event_focus);
          $$event(document.forms[i][j] , "blur"   , $$.prototype.efo.event_blur);
          $$event(document.forms[i][j] , "keyup"  , $$.prototype.efo.event_keyup);
          $$event(document.forms[i][j] , "change" , $$.prototype.efo.event_change);
        }
      }
    },
    // 入力フォームに内容クリアボタンを設置する（input textのみ）
    setValueClear : function(){
      // forms
      for(var i=0; i<document.forms.length; i++){
        // tags
        for(var j=0; j<document.forms[i].length; j++){
          if(document.forms[i][j].tagName !== "INPUT"
          || document.forms[i][j].type    !== "text"){continue;}
          document.forms[i][j].setAttribute("data-efo-valueClear" , "1");
        }
      }
    },
    checkForm : function(){
      // forms
      for(var i=0; i<document.forms.length; i++){
        // tags
        for(var j=0; j<document.forms[i].length; j++){
          $$.prototype.efo.require(document.forms[i][j]);
        }
      }
    },

    event_focus : function(e){
      var element = e.target;
      $$.prototype.efo.require(element);
    },
    event_blur : function(e){
      var element = e.target;
      $$.prototype.efo.require(element);
    },
    event_keyup : function(e){
      var element = e.target;
      $$.prototype.efo.require(element);
    },
    event_change : function(e){
      var element = e.target;
      $$.prototype.efo.require(element);
    },

    // 必須項目チェック
    require : function(element){
      if(!element){return;}
      if(element.required === true && element.value === ""){
        element.setAttribute("data-efo-empty","1");
      }
      else if(element.getAttribute("data-efo-empty")){
        element.removeAttribute("data-efo-empty");
      }
    }
  };

  // イベント関数
  var $$event = function(target, mode, func){
		//other Browser
		if (typeof target.addEventListener !== "undefined"){
      target.addEventListener(mode, func, false);
    }
    else if(typeof target.attachEvent !== "undefined"){
      target.attachEvent('on' + mode, function(){func.call(target , window.event)});
    }
  };
  $$.prototype.event = $$event;

  return $$;
})();