(function(){
  var MAIN = function(){
    this.init();
    this.set_event();
    this.common_lists();
  };

  MAIN.prototype.init = function(){

    // view-list-counts
    let elm = document.getElementById("list_count");
    if(elm){
      new $$lib().event(elm , "change" , (function(e){
        let target = e.target;
        window.localStorage.setItem("myntpage_blog_list_counts" , target.value);
        location.reload();
      }).bind(this));
    }
    
    let myntpage_blog_list_counts = window.localStorage.getItem("myntpage_blog_list_counts");
    if(myntpage_blog_list_counts && elm){
      elm.value = myntpage_blog_list_counts;
    }
    

    // status
    let status_area = document.querySelector(".status-area");
    if(status_area){
      let status_id = status_area.getAttribute("data-status");
      status_id = status_id ? Number(status_id) : 0;
      let status_lists = status_area.querySelectorAll(":scope > ul > li");
      for(let i=0; i<status_lists.length; i++){
        let current_status_id = status_lists[i].getAttribute("data-id");
        if(current_status_id == status_id){
          status_lists[i].setAttribute("data-active","1");
        }
        else{
          status_lists[i].setAttribute("data-active","0");
        }
      }
    }
    
  };

  MAIN.prototype.set_event = function(){
    let button_add = document.querySelectorAll(".add");
    if(button_add && button_add.length){
      for(let i=0; i<button_add.length; i++){
        new $$lib().event(button_add[i] , "click" , (function(){this.click_add()}).bind(this));
      }
    }
    let list_datas = document.querySelector(".lists table tbody.datas");
    if(list_datas){
      new $$lib().event(list_datas , "click" , (function(e){this.click_list(e)}).bind(this));
    }
    let status = document.querySelectorAll("ul.status > li");
    if(status && status.length){
      for(let i=0; i<status.length; i++){
        new $$lib().event(status[i] , "click" , (function(e){this.click_status(e)}).bind(this));
      }
    }
    let group_change = document.getElementById("group_id");
    if(group_change){
      new $$lib().event(group_change , "change" , (function(e){this.change_group(e)}).bind(this));
    }
  };

  MAIN.prototype.click_add = function(){
    let querys = [
      "c=blog/edit"
    ];
    let elm_type = document.getElementById("type");
    if(elm_type){
      querys.push("type="+ elm_type.value);
    }
    let group_id = document.getElementById("group_id");
    if(group_id && group_id.value){
      querys.push("group="+ group_id.value);
    }
    let urlinfo = new $$lib().urlinfo();
    let url = urlinfo.url +"?"+ querys.join("&");
    location.href = url;
  };

  MAIN.prototype.click_list = function(e){
    let target = e.target;
    if(!target){return;}
    let list = new $$lib().upperSelector(target , "tr[data-id]");
    if(!list){return;}
    let type = this.get_type();
    let id = list.getAttribute("data-id");
    let urlinfo = new $$lib().urlinfo();
    let querys = [
      "c=blog/edit",
      "id="+id
    ];
    if(type){
      querys.push("type="+ type);
    }
    let group_id_elm = document.getElementById("group_id");
    if(group_id_elm && group_id_elm.value){
      querys.push("group="+group_id_elm.value);
    }
    let url = urlinfo.url +"?"+ querys.join("&");
    location.href = url;
  };

  MAIN.prototype.get_type = function(){
    let type_elm = document.getElementById("type");
    return type_elm ? type_elm.value : 1;
  };

  MAIN.prototype.common_lists = function(){
    let page = document.getElementById("page").value;
    let type = this.get_type();
    let table = '.lists > table';
    let list_count = Number(document.getElementById("list_count").value);
    let urlinfo = new $$lib().urlinfo();
    let current_num = urlinfo.query.num || 0;
    let status = document.getElementById("status").value;
    let group  = document.getElementById("group_id").value;
    new $$list({
      debug : false,
      page  : page,
      table : table,
      template_name : {
        lists : "lists"
      },
      element : {
        lists_base  : table +" .datas",
        lists_empty : ".lists .empty",
        template_first_tag : "tr"
      },
      database_unique_keys : ["id","type"],
      load_php : '\\lib\\blog\\php\\data::load_lists_json_data('+type+','+list_count+','+current_num+','+status+','+group+')',
      loaded : (function(e){this.init_pagenation(e)}).bind(this)
    });
  };

//   MAIN.prototype.load_lists = function(){
//     let page = document.getElementById("page").value;
//     let type_elm = document.getElementById("type");
//     let type = type_elm ? type_elm.value : 1;
//     new $$ajax({
//       url : location.href,
//       query : {
//         php  : '\\page\\'+page+'\\contents\\blog\\php\\data::load_lists_json('+type+',5)',
//         exit : true
//       },
//       onSuccess : (function(res){
// // console.log(res);
//         if(!res){return;}
//         let json = JSON.parse(res);
//         for(let i=0; i<json.length; i++){
//           this.list_append(json[i]);
//           this.options.datas[json[i].id] = json[i];
//         }
//       }).bind(this)
//     });
//   };

  MAIN.prototype.init_pagenation = function(datas){
    // if(!datas || datas.status !== "ok"){return;}
console.log(datas);
    let loaded_datas = datas;
// console.log(datas);
    let page   = document.getElementById("page").value;
    let status = document.getElementById("status").value;
    let group_elm = document.getElementById("group_id");
    let group_id  = group_elm ? group_elm.value : "";
    let tag_elm   = document.getElementById("tag_id");
    let tag_id    = tag_elm ? tag_elm.value : "";
    let search_elm = document.querySelector("input[name='search']");
    let search_val = search_elm ? search_elm.value : "";
    let type   = 1;
    new $$ajax({
      url : location.href,
      query : {
        php  : '\\lib\\blog\\php\\data::data_count('+type+','+status+',"'+ group_id +'","'+ tag_id +'","'+ search_val +'")',
        exit : true
      },
      onSuccess : (function(res){
// console.log(res);
        let total_num   = res ? Number(res) : 0;
// console.log(total_num);
        if(!loaded_datas || !loaded_datas.length){return;}
        let urlinfo = new $$lib().urlinfo();
        let current_num = typeof urlinfo.query.num !== "undefined" && urlinfo.query.num ? Number(urlinfo.query.num) : 0;
        let view_count = Number(document.getElementById("list_count").value);
        let page_count = Math.ceil(total_num / view_count);
        new $$pagenation({
          target : ".pagenation",
          page_total    : page_count,
          page_count    : 5,
          query_current : "num",
          between_str   : ".."
        });
      }).bind(this)
    });
  };
  MAIN.prototype.view_pagenation = function(current_num , total_num , view_count){
    if(!total_num || view_count >= total_num){return;}
    let pagenation_area = document.querySelector(".pagenation-area");
    if(!pagenation_area){return;}
    let block_count_area = pagenation_area.querySelector("ul.block-count");
    if(!block_count_area){return;}
    let block_count = Math.round(total_num / view_count);
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

  MAIN.prototype.click_status = function(e){
    var target = e.currentTarget;
    if(!target){return;}
    let id = target.getAttribute("data-id");
    if(id === null){return;}
    let urlinfo = new $$lib().urlinfo();
    urlinfo.query.status = id;
    let arr = [];
    for(let i in urlinfo.query){
      if(id == 0 && i === "status"){continue;}
      arr.push(i+"="+urlinfo.query[i]);
    }
    let url = urlinfo.url +"?"+ arr.join("&");
    location.href = url;
  };

  MAIN.prototype.change_group = function(e){
    var target = e.target;
    if(!target){return;}
    let urlinfo = new $$lib().urlinfo();
    urlinfo.query.group = Number(target.value);
    let querys = [];
    for(let i in urlinfo.query){
      if(i==="group" && !urlinfo.query.group){continue;}
      querys.push(i+"="+urlinfo.query[i]);
    }
    let url = urlinfo.url +"?"+ querys.join("&");
    location.href = url;
  };


  new $$lib().construct(MAIN);
})();
