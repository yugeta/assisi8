;(function(){

  var $$ = function(){};

  /**********
	//style値を取得
	概要：対象項目のCSS値を取得
	param:element  対象項目
	**********/
	$$.prototype.getStyle=function(e,s){
		if(!s){return}
		//対象項目チェック;
		if(typeof(e)=='undefined' || e==null || !e){
			e = $b;
		}
		//属性チェック;
		var d='';
		if(typeof(e.currentStyle)!='undefined'){
			d = e.currentStyle[$$.prototype.camelize(s)];
			if(d=='medium'){
				d = "0";
			}
		}
		else if(typeof(document.defaultView)!='undefined'){
			d = document.defaultView.getComputedStyle(e,'').getPropertyValue(s);
		}
		return d;
	};

	//スタイルシートの値を読み出す
	$$.prototype.getCSS = function(css , selTxt , styleName){
		if(!css || !selTxt){return}

		if(styleName){
			for(var j=0;j<css.cssRules.length;j++){
				if(css.cssRules[j].selectorText==selTxt){
					return css.cssRules[j].style[styleName];
				}
			}
		}
		else{
			for(var j=0;j<css.cssRules.length;j++){
				if(css.cssRules[j].selectorText==selTxt){
					return css.cssRules[j].cssText;
				}
			}
		}
	};

	//特定のselector情報にcss設定を追加
	$$.prototype.setCSS = function(css , selTxt , styleName , value){
		if(!css || !selTxt || !styleName){return}

		//selectorTextの指定がある場合
		for(var j=0;j<css.cssRules.length;j++){
			if(css.cssRules[j].selectorText==selTxt){
				css.cssRules[j].style[styleName] = value;
				return true;
			}
		}

		//対象セレクタが無い場合
		css.addRule(selTxt , styleName+":"+value);

	};

	//特定のselectorからcss設定を削除
	$$.prototype.delCSS = function(css , selTxt , styleName){
		if(!css || !selTxt){return}
		if(!css.cssRules){return}

		//selectorTextの指定がある場合
		for(var j=css.cssRules.length-1;j>=0;j--){
			if(css.cssRules[j].selectorText && css.cssRules[j].selectorText.match(selTxt)){
			}
		}
	};

	//rgb(**,**,**) -> #**
	$$.prototype.rgb2bit16 = function(col){
		if(col.match(/rgb(.*?)\((.*)\)/)){
			var rgb = RegExp.$2.split(",");
			var val="#";
			for(var i=0;i<3;i++){
				var val2 = parseInt(rgb[i],10).toString(16);
				if(val2.length==1){
					val+="0"+val2;
				}
				else{
					val+= val2;
				}
			}
			col = val;
		}
		return col;
	};

	//ハイフン区切りを大文字に変換する。
	$$.prototype.camelize = function(v){
		if(typeof(v)!='string'){return}
		return v.replace(/-([a-z])/g , function(m){return m.charAt(1).toUpperCase();});
	};

  $$SearchStyle = $$;
})();
