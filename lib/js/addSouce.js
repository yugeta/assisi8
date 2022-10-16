;$$addSouce = (function(){
  var $$addSouce = function(file){
    var sp = file.split(".");
    var s;

    // css
    if(sp[sp.length-1] === "css"){
      s = document.createElement("link");
      s.type = "text/css";
      s.href = file + "?" +(+new Date());
    }

    // javascript
    else{
      s = document.createElement("script");
      s.src = file + "?" +(+new Date());
    }
    document.body.appendChild(s);
  }
  return $$addScript;
})();