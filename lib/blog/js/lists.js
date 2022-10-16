;(function(){
  let __options = {
    
  };


	var MAIN = function(){
    this.load_lists();
    // this.set_pagenation();
    // this.load_eyecatch();
    this.set_event();
  };

  MAIN.prototype.load_lists = function(){
    let page = document.getElementById("page");
    if(!page || !page.value){return;}
    let urlinfo = new $$lib().urlinfo();
    let release_status_elm = document.getElementById("release_status");
    let list_count_elm     = document.getElementById("list_count");

    let type            = urlinfo.query.type || 1;
    let count           = list_count_elm ? list_count_elm.value : 10;
    let current_num     = urlinfo.query.num || 0;
    let release_status  = release_status_elm ? release_status_elm.value : "";
    let group_id        = urlinfo.query.group || "";
    let tag             = urlinfo.query.tag || "";
    // let still_today_flg = true;
    let search          = urlinfo.query.search || "";
// console.log(type+"/"+count+"/"+current_num+"/"+release_status+"/"+group_id+"/"+tag+"/"+still_today_flg+"/"+search);
//$type=1 , $count=10 , $current_num=0 , $status=0 , $group_id=null , $search=""
    new $$ajax({
      url : location.href,
      query : {
        php  : '\\lib\\blog\\php\\data::load_lists_json('+type+','+count+','+current_num+','+release_status+',"'+group_id+'","'+tag+'","'+search+'")',
        exit : true
      },
      onSuccess : (function(res){
// console.log(res);
        if(!res){return;}
        let datas = JSON.parse(res);
// console.log(datas);
        if(datas.status !== "ok"){return;}
        this.view_li(datas.data);
        this.set_pagenation(datas.total_count);
        this.load_eyecatch();
      }).bind(this)
    });
  };
  MAIN.prototype.view_li = function(datas){
// console.log(datas);
    if(!datas || !datas.length){return;}
    let tmp_base = this.get_template("lists");
    if(!tmp_base){return;}
    // let lists = document.querySelector(".lists");
    let lists = document.querySelector(".blog-lists .lists");
    if(!lists){return;}
    for(let data of datas){
      // console.log(data);
      // let tmp = tmp_base;
      let li = this.tmp2data_value(tmp_base , data);
      lists.insertAdjacentHTML("beforeend" , li);
    }
  };
  MAIN.prototype.tmp2data_value = function(tmp , data){
    let reg = new RegExp('{{(.*?)}}','g');
    let arr = [];
    while ((res = reg.exec(tmp)) !== null) {
      arr.push(res[1]);
    }
    for(let key of arr){
      let val = data[key] || "";
      tmp = tmp.split('{{'+String(key)+'}}').join(val);
    }
    return tmp;
  };


  MAIN.prototype.get_template = function(key){
    if(!key){return null;}
    let elm = document.querySelector(".template [data-key='"+key+"']");
    if(!elm){return null;}
    return elm.innerHTML;
  };

  MAIN.prototype.click_list = function(e){
    let target = e.target;
    if(!target){return;}
    let li = new $$lib().upperSelector(target , "li");
    if(!li){return;}
    let id = li.getAttribute("data-id");
    if(!id){return;}
    let urlinfo = new $$lib().urlinfo();
    let url = urlinfo.url +"?b="+ id;
    if(typeof urlinfo.query.type !== "undefined" && urlinfo.query.type){
      url += "&t="+urlinfo.query.type;
    }
    location.href = url;
  };
	
	// アイキャッチ画像表示
	MAIN.prototype.load_eyecatch = function(){
    let images = document.querySelectorAll("img.eyecatch[data-src]");
    if(!images || !images.length){return;}
    for(let i=0; i<images.length; i++){
      let src = images[i].getAttribute("data-src");
      images[i].removeAttribute("data-src");
      if(!src){continue;}
      images[i].onload = (function(){this.load_eyecatch();}).bind(this);
      images[i].setAttribute("src" , src);
      images[i].removeAttribute("data-view-size");
      break;
    }
  };
  

  // MAIN.prototype.set_pagenation = function(){
  //   let lists = document.querySelectorAll(".lists .article");
  //   this.init_pagenation(lists.length);
  // };
  // MAIN.prototype.init_pagenation = function(load_count){
  //   let page  = document.getElementById("page").value;
  //   let type  = 1;
  //   let group = document.getElementById("group").value;
  //   let tag   = document.getElementById("tag").value;
  //   new $$ajax({
  //     url : location.href,
  //     query : {
  //       php  : '\\lib\\blog\\php\\lists::data_count('+type+','+group+','+tag+',true)',
  //       // php  : '\\page\\'+page+'\\contents\\blog\\php\\lists::data_count('+type+','+group+','+tag+',true)',
  //       // php  : '\\page\\'+page+'\\contents\\blog\\php\\data::load_count_('+type+','+group+','+tag+')',
  //       exit : true
  //     },
  //     onSuccess : (function(res){
  //       let total_num   = res ? Number(res) : 0;
  //       if(!load_count){return;}
  //       let urlinfo = new $$lib().urlinfo();
  //       let current_num = typeof urlinfo.query.num !== "undefined" && urlinfo.query.num ? Number(urlinfo.query.num) : 0;
  //       let count_elm = document.getElementById("list_count");
  //       if(count_elm){
  //         let view_count = Number(count_elm.value);
  //         let page_count = Math.ceil(total_num / view_count);
  //         new $$pagenation({
  //           target : ".pagenation",
  //           page_total    : page_count,
  //           page_count    : 5,
  //           query_current : "num",
  //           between_str   : ".."
  //         });
  //       }
  //     }).bind(this)
  //   });
  // };
  MAIN.prototype.set_pagenation = function(total_num){
    if(!total_num){return;}
    let urlinfo = new $$lib().urlinfo();
    // let current_num = typeof urlinfo.query.num !== "undefined" && urlinfo.query.num ? Number(urlinfo.query.num) : 0;
    let count_elm = document.getElementById("list_count");
    if(count_elm){
      let view_count = Number(count_elm.value);
      let page_count = Math.ceil(total_num / view_count);
      new $$pagenation({
        target : ".pagenation",
        page_total    : page_count,
        page_count    : 5,
        query_current : "num",
        between_str   : ".."
      });
    }
  };
  MAIN.prototype.view_pagenation = function(current_num , total_num , view_count){
    if(!total_num || view_count >= total_num){return;}
    let pagenation_area = document.querySelector(".pagenation-area");
    if(!pagenation_area){return;}
    let block_count_area = pagenation_area.querySelector("ul.block-count");
    if(!block_count_area){return;}
    let block_count = Math.ceil(total_num / view_count);
    let li;
    for(let i=0; i<block_count; i++){
      li = document.createElement("li");
      li.setAttribute("data-num" , i);
      li.textContent = String(i+1);
      if(i === current_num){
        li.setAttribute("class" , "active");
      }
      block_count_area.appendChild(li);
    }
    new $$lib().event(block_count_area , "click" , (function(e){this.click_pagenation(e)}).bind(this));
  }
  MAIN.prototype.click_pagenation = function(e){
    let current = e.target;
    if(!current){return;}
    let target = new $$lib().upperSelector(current , "li");
    if(!target){return;}
    if(target.classList.contains("active")){return;}
    let num = target.getAttribute("data-num");
    let urlinfo = new $$lib().urlinfo();
    urlinfo.query.num = num;
    let querys = [];
    for(let i in urlinfo.query){
      querys.push(i+"="+urlinfo.query[i]);
    }
    let url = urlinfo.url +"?"+ querys.join("&");
    location.href = url;
  };

  MAIN.prototype.set_event = function(){
    let group_select = document.getElementById("group_id");
    if(group_select){
      new $$lib().event(group_select , "change" , this.go_search.bind(this));
    }

    let tag_select = document.getElementById("tag_id");
    if(tag_select){
      new $$lib().event(tag_select , "change" , this.go_search.bind(this));
    }

    let search_button = document.querySelector("button[name='search_button']");
    if(search_button){
      new $$lib().event(search_button , "click" , this.go_search.bind(this))
    }
  };
//   MAIN.prototype.change_group = function(e){
//     let select = e.target;
//     let group_id = select.value;
//     let urlinfo = new $$lib().urlinfo();
//     if(group_id){
//       location.href = urlinfo.url +"?c="+ urlinfo.query.c +"&group="+ group_id;
//     }
//     else{
//       location.href = urlinfo.url +"?c="+ urlinfo.query.c;
//     }
//   };
//   MAIN.prototype.change_tag = function(e){
//     console.log("tag");
//   };
//   MAIN.prototype.click_search = function(e){
//     // console.log("search");
//     let search_input = document.querySelector("input[name='search']");
// // console.log(search_input);
//     if(!search_input){return;}
//     let str = search_input.value;
//     // console.log(str);
//     let urlinfo = new $$lib().urlinfo();
//     if(str){
//       location.href = urlinfo.url +"?c="+ urlinfo.query.c +"&search="+ str.trim();
//     }
//     else{
//       location.href = urlinfo.url +"?c="+ urlinfo.query.c;
//     }
//   };
  MAIN.prototype.go_search = function(){
    let urlinfo = new $$lib().urlinfo();
    let url = urlinfo.url;
    let querys = ["c="+urlinfo.query.c];

    let group_select = document.getElementById("group_id");
    if(group_select && group_select.value){
      querys.push("group="+ group_select.value);
    }

    let tag_select = document.getElementById("tag_id");
    if(tag_select && tag_select.value){
      querys.push("tag="+ tag_select.value);
    }

    let search_input = document.querySelector("input[name='search']");
    if(search_input && search_input.value){
      querys.push("search="+ encodeURI(search_input.value.trim()));
    }

    location.href = url +"?"+ querys.join("&");
  };


	new $$lib().construct(MAIN);
})();
