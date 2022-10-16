/**
 * ブラウザIDを扱うスクリプト
 * 
 * 【概要】
 * - システムで利用するブラウザ認証用のユーザーIDを生成する
 * - cookie , local-storage に対して、無限期間にて保存する
 *  - cookieに保存するのは、phpでもデータを拾えるようにするため。
 * - データの優先順位
 *  cookie > localstorage
 *  但し、どちらかが存在してどちらかがない場合は、相互にデータを補填する。
 *  値が食い違っている場合、cookieの値をlocalstorageに上書きする。
 * 
 * 【用語】
 * - BID : Browser-ID
 * - UID : Unique-User-ID
 * 
 * 【仕様】
 * - Format       : jsonフォーマット : {bid:"",access:""}
 * - Data-storage : 呼び値を複数保持 : local-storage > cookie
 * - first-access : 初回アクセス虹値 : idから取得
 * - last-access  : 最終アクセス日時 : yyyymmddhhiiss
 * - count        : アクセス階数
 * 
 */

;$$bid = (function(){

  // 基本モジュール
  var $$ = function(){

    // pickup-bid
    var bid = this.getBID();

    // Create it. if it doesn't exist.
    if(!bid){
      bid = this.makeBID();
    }

    this.setBID(bid);

    this.bid = bid;
  };

  // options
  $$.prototype.options = {
    main_key : "myntpage",
    period   : 60*60*24*365
  };

  $$.prototype.getBidkeyName = function(){
    return this.options.main_key+"_bid";
  };

  // Make-ID (msec(99999999999999).rnd(999)) -> (max 100 count)
  $$.prototype.makeBID = function(){
    var msecStr = (+new Date);
    var rndStr  = Math.floor(Math.random() * Math.floor(999));
    return msecStr +"."+ String(rndStr).slice(-3);
  };

  // Save-ID (cookie & localstorage)
  $$.prototype.setBID = function(bid){
    // data
    var data = $$.prototype.makeData(bid);
    var key = this.getBidkeyName();

    // Cookie
    new COOKIE().set(key , bid);

    // localStorage
    this.setBID_Storage(key,JSON.stringify(data));
  };

  $$.prototype.makeData = function(bid){
    return {
      bid    : bid,
      update : (+new Date()),
      period : this.periodTime(this.options.period)
    };
  };

  $$.prototype.periodTime = function(period){
    startTime = (+new Date());
    var endTime = startTime + (period*1000);
    return new Date(endTime).getTime();
  };

  $$.prototype.getEntry = function(){
    var json = this.get_storage(this.getBidkeyName());

    if(json && json.entry){
      return json.entry
    }
    else{
      return (+new Date());
    }
  };

  // set Storage
  $$.prototype.setBID_Storage = function(key , data){
    if(typeof window.localStorage !== "undefined"){
      localStorage.setItem(key , data);
      return true;
    }
    else{
      return false;
    }
  };

  // Load-ID [(string)bid or (bool)false]
  $$.prototype.getBID = function(){
    var key = this.getBidkeyName();
    var bid = new COOKIE().get(key);
    if(bid){return bid;}
    // LocalStorage
    data = this.get_storage(key);
    return (data && data.bid) ? data.bid : null;
  };

  // get Storage
  $$.prototype.get_storage = function(key){
    if(typeof window.$$cookie !== "undefined"){
      var data = localStorage.getItem(key);
      return JSON.parse(data);
    }
    else{
      return false;
    }
  };


  var COOKIE = function(){
    this.options = {
      sec : (24*60*60*1000)
    };
  };
  COOKIE.prototype.get = function(name){
    if(!name){return null;}
    var cookies = document.cookie.split(";");
    for (var i=0; i<cookies.length; i++){
      var sp = cookies[i].split("=");
      var key = sp[0].trim();
      var val = sp.slice(1).join("=").trim();
      if (key === name) {
        return val;
      }
    }
    return '';
  };
  COOKIE.prototype.set = function(key , val){
    if(!key){return false;}
    // val  = this.encode(val);
    if (this.checkSecure()) {
      document.cookie = key + "=" + val + ";expires=" + this.expires() + ";secure";
    }
    else {
      document.cookie = key + "=" + val + ";expires=" + this.expires();
    }
    return true;
  };
  COOKIE.prototype.expires = function(sec){
    var exp = new Date();
    exp.setTime(exp.getTime() + (this.options.sec  * 1000));
    return exp.toGMTString();
  };
  COOKIE.prototype.checkSecure = function(){
    if (location.href.match(/^https/)) {
      return true;
    }
    else {
      return false;
    }
  };



  return new $$().bid;
  // console.log(bid);
  // console.log("first : "+ bid.getBID());
  // return bid;
})();