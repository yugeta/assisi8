;var elementSelector = (function(){

  // 上位階層からみたカレントエレメントの順番を取得
  var getChildNumber = function(element){
    if(!element){return}
    var parent = element.parentElement;
    var children = parent.children;
    var number = 1;
    for(var i=0; i<children.length; i++){
      if(children[i] === element){
        break;
      }
      number++;
    }
    return number;
  };
  
  var $$ = function(element){
    if(!element){return;}
    
    // 上位ID値を持っているエレメントを検索
    var selectors = [];
    for (var cur=element; cur; cur=cur.parentElement) {
      if (cur.id) {
        selectors.unshift("#"+cur.id);
        break;
      }
      else if(cur.tagName === "BODY"){
        selectors.unshift("body");
        break;
      }
      var tag = cur.tagName.toLowerCase();
      var nthChild = getChildNumber(cur);
      nthChild = (nthChild === 1) ? "" : ":nth-child("+nthChild+")";
      var className = (cur.className && cur.className.indexOf(" ") !== -1) ? "."+cur.className : "";
      selectors.unshift(tag + className +nthChild);
    }
    return selectors.join(" > ");
  };
  
  return $$;
})();