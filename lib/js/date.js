window.$$date = (function(){
  function MAIN(){

  }

  MAIN.prototype.date_ymd_object = function(dt){
    if(!dt){return null;}
    return {
      y : dt.getFullYear(),
      m : dt.getMonth()+1,
      d : dt.getDate()
    };
  };

  MAIN.prototype.date_diff = function(current_date , diff){
    if(!current_date){return null;}
    diff = diff || 1;
    let utime = Date.parse(current_date);
    let dt = new Date(utime);
    dt.setDate(dt.getDate() + diff);
    return dt;
  };

  // 2つの日付の間が何日なのか、日数を得る
  // ex) 2021.10.1 -> 2021.10.2 = +1
  MAIN.prototype.between_date_count = function(date1 , date2){
    let utime1 = Date.parse(date1);
    let utime2 = Date.parse(date2);
    let diff = (utime2 - utime1);
    return Number(diff/(60*60*24)/1000);
  };
  // 2つの日付の表示日数を求める（チャートなどでのrangeではこっちを使う）
  // ex) 2021.10.1 -> 2021.10.2 = +1
  MAIN.prototype.count_dates = function(date1 , date2){
    let utime1 = Date.parse(date1);
    let utime2 = Date.parse(date2);
    let diff = (utime2 - utime1);
    return Number(diff/(60*60*24)/1000) + 1;
  };

  return MAIN;
})();