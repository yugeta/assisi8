使い方サンプル
==


# sameple - 管理画面でmodal対応

  let uid  = document.getElementById("uid").value;
  let page = document.getElementById("page").value;
  let type = document.querySelector(".select select").value;
  let table = 'q';
  new $$list({
    debug : false,
    uid   : uid,
    page  : page,
    table : table,
    click_mode : "modal",
    query : {
      lang : type
    },
    template_name : {
      lists : "lists",
      modal : "modal"
    },
    element : {
      modal_add   : ".add button",
      lists_base  : ".lists .datas",
      lists_empty : ".lists .empty"
    },
    modal : {
      width : "calc(100% - 20px)",
      title : type +" Program問題作成"
    },
    save_php : '\\lib\\lists\\common::save_json("'+uid+'","'+page+'","'+table+'")',
    load_php : '\\lib\\lists\\common::load_jsons("'+ uid +'","'+ page +'","'+ table +'")'
  });

# sample - リスト表示して、クリックしたら独自関数で処理

  let uid  = document.getElementById("uid").value;
  let page = document.getElementById("page").value;
  let type = document.querySelector(".select select").value;
  let table = 'q';
  new $$list({
    debug : false,
    uid   : uid,
    page  : page,
    table : table,
    click_mode : "action",
    click_function : (function(e){this.click_lists(e)}).bind(this),
    query : {
      lang : type
    },
    template_name : {
      lists : "lists"
    },
    element : {
      lists_base  : ".lists .datas",
      lists_empty : ".lists .empty"
    },
    save_php : '',
    load_php : '\\lib\\lists\\common::load_jsons("'+ uid +'","'+ page +'","'+ table +'")'
  });


