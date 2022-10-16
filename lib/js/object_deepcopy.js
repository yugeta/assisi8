// Global関数
$$deepcopy = (function(){
  var MAIN = function(objects){
    return this.deepcopy(objects);
  };

  MAIN.prototype.deepcopy = function(objects){
    if(objects === null
    || objects === undefined){return null;}

    var newObjects;

    switch(typeof objects){
      case "undefined":

      case "string":
      case "number":
      case "function":
        newObjects = objects;
        break;

      case "object":
        newObjects = typeof objects.length !== "undefined" ? [] : {};
        for(var i in objects){
          newObjects[i] = this.deepcopy(objects[i]);
        }
        break;
    }
    return newObjects;
  };
  return MAIN;
})();





/**
 * Memo
 * 
 * # functionのコピー
 *  - var fnc = function(){...}
 * 
 * # 配列と連想配列の入り混じったobject
 *  - var arr = [1,2,3,4,5];
 *    arr.test = "hoge";
 * 
 * 
 */