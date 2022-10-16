module.exports = (function(){
  var MAIN =  function(p){
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

		var query  = (p2[1])?p2[1]:"";
		var q_arr  = query.split("&");
		var querys = {};
		for(var i in q_arr){
			var sp = q_arr[i].split("=");
			querys[sp[0]] = sp[1];
		}

		return {
			hostname  : urls[2],
			basename  : basename,
			dirname   : dirname.join("/"),
			filename  : filename.join("."),
			extension : ext,
			query     : query,
			querys    : querys,
      path      : p2[0]
    };
  };
  return MAIN
})();