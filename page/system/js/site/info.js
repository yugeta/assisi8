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
    var comments = document.querySelectorAll("#infoLists .comment pre");
    for(var i=0; i<comments.length; i++){
      $$event(comments[i] , "click" , (function(e){this.toggleCommentExpand(e)}).bind(this));
    }
  }

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

  new $$;
})();