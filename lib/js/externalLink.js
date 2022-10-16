;$$externalLink = (function(){
	// type @ ["after"(default) , "before"]
	var $$ = function(type , elm){
		$$.prototype.setStyle(type);
		var links = $$.prototype.searchLinks();
		for(var i=0; i<links.length; i++){
			if($$.prototype.isExternalLink(links[i])){
				$$.prototype.setTargetBlank(links[i]);
				$$.prototype.addIcon(links[i]);
			}
		}
	};
	
	// ページ内のリンク一覧の取得
	$$.prototype.searchLinks = function(){
		return document.links;
	};
	
	// URLから外部リンクの判断
	$$.prototype.isExternalLink = function(element){
		var localdomain = location.host;
		var linkDomain = element.href.split("/")[2];
		
		// 別ドメイン（外部ドメイン）
		if(localdomain !== linkDomain){
			return true;
		}
		// 同一ドメイン（内部リンク）
		else{
			return false;
		}
	};
	
	// 別タブで開く処理追加
	$$.prototype.setTargetBlank = function(element){
		element.target = "_blank";
	};
	
	// add style
	$$.prototype.setStyle = function(type){
		type = (type) ? type : "after";
		if(document.getElementById("externalLink")){return;}
		var style = document.createElement("style");
		style.type = "text/css";
		style.id = "eternalLink";
		var css = "";
		css += 'a[data-externalLink="1"]:'+type+'{';
		css += 'content:"  ";';
		css += 'background:url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjwhRE9DVFlQRSBzdmcgIFBVQkxJQyAnLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4nICAnaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkJz48c3ZnIGlkPSJMYXllcl8xIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA2NCA2NDsiIHZlcnNpb249IjEuMSIgdmlld0JveD0iMCAwIDY0IDY0IiB4bWw6c3BhY2U9InByZXNlcnZlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj48c3R5bGUgdHlwZT0idGV4dC9jc3MiPgoJLnN0MHtmaWxsOiMxMzQ1NjM7fQo8L3N0eWxlPjxnPjxnIGlkPSJJY29uLUV4dGVybmFsLUxpbmsiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDM4Mi4wMDAwMDAsIDM4MC4wMDAwMDApIj48cG9seWxpbmUgY2xhc3M9InN0MCIgaWQ9IkZpbGwtMTE4IiBwb2ludHM9Ii0zNTIuMywtMzQzLjQgLTM1NC42LC0zNDUuNyAtMzI4LjgsLTM3MS40IC0zMjYuNiwtMzY5LjIgLTM1Mi4zLC0zNDMuNCAgICAiLz48cG9seWxpbmUgY2xhc3M9InN0MCIgaWQ9IkZpbGwtMTE5IiBwb2ludHM9Ii0zMjYsLTM1NC45IC0zMjkuNCwtMzU0LjkgLTMyOS40LC0zNjguNiAtMzQzLjEsLTM2OC42IC0zNDMuMSwtMzcyIC0zMjYsLTM3MiAgICAgIC0zMjYsLTM1NC45ICAgICIvPjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0tMzM0LjYtMzI0aC0zNC4zYy0yLjgsMC01LjEtMi4zLTUuMS01LjF2LTM0LjNjMC0yLjgsMi4zLTUuMSw1LjEtNS4xaDE4Ljl2My40aC0xOC45ICAgICBjLTAuOSwwLTEuNywwLjgtMS43LDEuN3YzNC4zYzAsMC45LDAuOCwxLjcsMS43LDEuN2gzNC4zYzAuOSwwLDEuNy0wLjgsMS43LTEuN1YtMzQ4aDMuNHYxOC45Qy0zMjkuNC0zMjYuMy0zMzEuNy0zMjQtMzM0LjYtMzI0ICAgICAiIGlkPSJGaWxsLTEyMCIvPjwvZz48L2c+PC9zdmc+);';
		css += 'background-size:contain;';
		css += 'width:16px;';
		css += 'height:16px;';
		
		css += 'vertical-align:middle;';
		css += 'display:inline-block;';
		css += 'margin:0 4px;';
		css += '}\n';
		style.innerHTML = css;
		document.getElementsByTagName("head")[0].appendChild(style);
	};
	
	// アイコン追加
	$$.prototype.addIcon = function(element){
		element.setAttribute("data-externalLink" , "1");
	};
	
	
	return $$;
})();