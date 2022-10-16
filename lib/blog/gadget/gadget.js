(function(){
  var MAIN = function(){
    this.set_group();
    this.set_tag();
    this.set_sns();
    this.set_relation();
  };

  MAIN.prototype.set_group = function(){
    let group_links = document.querySelectorAll(".gadget-groups > li");
    if(!group_links || !group_links.length){return;}
    for(let i=0; i<group_links.length; i++){
      new $$lib().event(group_links[i] , "click" , (function(e){
        let target = e.currentTarget;
        if(!target){return;}
        let group_id = target.getAttribute("data-id");
        let urlinfo = new $$lib().urlinfo();
        urlinfo.query.c = "blog";
        urlinfo.query.group = group_id;
        let querys = [];
        for(let i in urlinfo.query){
          if(i === "b"){continue;}
          if(i === "group" && !group_id){continue;}
          querys.push(i+"="+urlinfo.query[i]);
        }
        location.href = urlinfo.url +"?"+ querys.join("&");
      }).bind(this));
    }
  };
  MAIN.prototype.set_tag = function(){
    let links = document.querySelectorAll(".gadget-tags > li");
    if(!links || !links.length){return;}
    for(let i=0; i<links.length; i++){
      new $$lib().event(links[i] , "click" , (function(e){
        let target = e.currentTarget;
        if(!target){return;}
        let tag = target.getAttribute("data-tag");
        let urlinfo = new $$lib().urlinfo();
        urlinfo.query.c = "blog";
        urlinfo.query.tag = tag;
        let querys = [];
        for(let i in urlinfo.query){
          if(i === "b"){continue;}
          if(i === "tag" && !tag){continue;}
          querys.push(i+"="+urlinfo.query[i]);
        }
        location.href = urlinfo.url +"?"+ querys.join("&");
      }).bind(this));
    }
  };
  MAIN.prototype.set_sns = function(){

    
  };
  MAIN.prototype.set_relation = function(){

    
  };



  new $$lib().construct(MAIN);
})();
