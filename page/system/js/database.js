;(function(){
  var __event = function(target, mode, func){
		if (target.addEventListener){target.addEventListener(mode, func, false)}
		else{target.attachEvent('on' + mode, function(){func.call(target , window.event)})}
	};

	var __urlinfo = function(uri){
    uri = (uri) ? uri : location.href;
    var data={};
    var urls_hash  = uri.split("#");
    var urls_query = urls_hash[0].split("?");
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

	var __construct = function(){
    switch(document.readyState){
      case "complete"    : new $$;break;
      case "interactive" : __event(window , "DOMContentLoaded" , function(){new $$});break;
      default            : __event(window , "load" , function(){new $$});break;
		}
  };

  var $$ = function(){
    new $$mynt_ajax({
      url : "system.php",
      query : {
        php : "\\mynt\\system\\database::getDataConfig()"
      },
      onSuccess : (function(res){
        if(!res){return;}
        var json = JSON.parse(res);
        if(typeof json.status === "undefined"){return;}
        if(json.status === "error"){return;}

        this.pageLoaded_setting(json.data);

        var elm_type = document.querySelector("select[name='data[type]']");
        if(elm_type){
          this.changeType({currentTarget:elm_type});
        }

        if(typeof json.data.tables !== "undefined" && Object.keys(json.data.tables).length){
          this.setTables(json.data.tables);
        }
        

      }).bind(this)
    });
    
    // type-change
    var elm_type = document.querySelector("select[name='data[type]']");
    if(elm_type){
      __event(elm_type , "change" , (function(e){this.changeType(e)}).bind(this));
    }
  };

  $$.prototype.pageLoaded_setting = function(data){
    if(!data){return;}

    // type
    var elm = document.querySelector("select[name='data[type]']");
    if(elm && typeof data.type !== "undefined"){
      elm.value = data.type;
      // console.log("type : ok");
    }
    // else{
    //   console.log("type : ng");
    // }

    // dir
    var elm = document.querySelector("input[name='data[dir]']");
    if(elm && typeof data.dir !== "undefined"){
      elm.value = data.dir;
      // console.log("dir  : ok");
    }
    // else{
    //   console.log("dir  : ng");
    // }

    // addr
    var elm = document.querySelector("input[name='data[addr]']");
    if(elm && typeof data.addr !== "undefined"){
      elm.value = data.addr;
      // console.log("addr : ok");
    }
    // else{
    //   console.log("addr : ng");
    // }

    // host
    var elm = document.querySelector("input[name='data[host]']");
    if(elm && typeof data.host !== "undefined"){
      elm.value = data.host;
      // console.log("host : ok");
    }
    // else{
    //   console.log("host : ng");
    // }

    // port
    var elm = document.querySelector("input[name='data[port]']");
    if(elm && typeof data.port !== "undefined"){
      elm.value = data.port;
      // console.log("port : ok");
    }
    // else{
    //   console.log("port : ng");
    // }

    // user
    var elm = document.querySelector("input[name='data[user]']");
    if(elm && typeof data.user !== "undefined"){
      elm.value = data.user;
      // console.log("user : ok");
    }
    // else{
    //   console.log("user : ng");
    // }

    // pass
    var elm = document.querySelector("input[name='data[pass]']");
    if(elm && typeof data.pass !== "undefined"){
      elm.value = data.pass;
      // console.log("pass : ok");
    }
    // else{
    //   console.log("pass : ng");
    // }

    // database
    var elm = document.querySelector("input[name='data[database]']");
    if(elm && typeof data.database !== "undefined"){
      elm.value = data.database;
      // console.log("database : ok");
    }
    // else{
    //   console.log("database : ng");
    // }
  };

  $$.prototype.changeType = function(e){
    var target = e.currentTarget;
    if(!target){return;}

    var property_types = document.querySelectorAll(".property > *");
    if(!property_types){return;}
    for(var i=0; i<property_types.length; i++){

      if(property_types[i].getAttribute("data-type") === target.value){
        property_types[i].setAttribute("data-visible" , "1");
        // console.log("property : "+property_types[i].getAttribute("data-type") +" : view");
      }
      else{
        property_types[i].setAttribute("data-visible" , "0");
        // console.log("property : "+property_types[i].getAttribute("data-type") +" : hidden");
      }
    }
  };

  $$.prototype.setTables = function(tables){
    var tables_area = document.querySelector(".tables-area");
    if(!tables_area){return;}

    var tables_temp = document.querySelector(".tables-temp");
    if(!tables_temp){return;}
    var temp = tables_temp.innerHTML;

    var column_elm = tables_temp.querySelector("column-area");
    var column_temp = column_elm.innerHTML;

    for(var table_name in tables){
      var div1 = document.createElement("div");
      div1.innerHTML = temp;

      // table-name
      var elm_name = div1.querySelector("[data-id='table_name']");
      elm_name.value = table_name;

      // data-dir
      var elm_dir = div1.querySelector("[data-id='dir']");
      elm_dir.value = tables[table_name].info.dir;

      // data-type
      var elm_type = div1.querySelector("[data-id='type']");
      elm_type.value = tables[table_name].info.type;

      //columns
      var column_area = div1.querySelector("column-line");
      // 一旦サンプル内容をクリアする
      column_area.innerHTML = "";
      for(var i=0; i<tables[table_name].columns.length; i++){
        
      }


      tables_area.appendChild(div1);
    }
  };


  __construct();
})();