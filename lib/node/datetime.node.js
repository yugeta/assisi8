/**
* 日付、時間関連の簡単取得ライブラリ
*/
module.exports = (function(){

  var $$ = function(){};

  // YYYYMMDD
  $$.prototype.getCurrentDate = function(){
    var d = new Date();
    var year  = (d.getFullYear()).toString();
    var month = ( Array(2).join("0") + (d.getMonth()+1) ).slice( -2 );
    var day   = ( Array(2).join("0") + (d.getDate()) ).slice( -2 );
    return year + month + day;
  };

  // HHIISS
  $$.prototype.getCurrentTime = function(){
    var d = new Date();
    var hour  = ( Array(2).join("0") + (d.getHours()) ).slice( -2 );
    var min   = ( Array(2).join("0") + (d.getMinutes()) ).slice( -2 );
    var sec   = ( Array(2).join("0") + (d.getSeconds()) ).slice( -2 );
    return hour + min + sec;
  };

  // YYYYMMDDHHIISS
  $$.prototype.getCurrentDateTime = function(){
    var d = new Date();
    var year  = d.getFullYear().toString();
    var month = ( Array(2).join("0") + (d.getMonth()+1) ).slice( -2 );
    var day   = ( Array(2).join("0") + (d.getDate()) ).slice( -2 );
    var hour  = ( Array(2).join("0") + (d.getHours()) ).slice( -2 );
    var min   = ( Array(2).join("0") + (d.getMinutes()) ).slice( -2 );
    var sec   = ( Array(2).join("0") + (d.getSeconds()) ).slice( -2 );
    return year + month + day + hour + min + sec;
  };

  return {
    date     : $$.prototype.getCurrentDate,
    time     : $$.prototype.getCurrentTime,
    datetime : $$.prototype.getCurrentDateTime
  };
})();
