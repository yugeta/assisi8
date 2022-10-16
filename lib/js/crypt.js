/*
  1:1:data

  data-type(encode)
  - : base64
  1 : 文字列 -> 文字コード -> decodeURIComponent -> base64
  2 : 
  3 : 
  4 : 

  # sample
  {"status":{"lv":0,"hp":10,"max_hp":10,"mp":0,"max_mp":0,"st":3,"pt":3,"ex":0,"gd":0,"doku":null,"battle_count":0,"item":[],"magic":[]},"stages":{"test/02":{"message_id":{"data_remove":"removed","game_start":"0"}},"test/javascript_01":{"object":[{"name":"gate_01","del":1}]}}}
  5:XY/RDoMgDEW/pu8OY4iPY/yHYUrQDJSEYvj8OSvJ6ts9J7dNC1KBEAkN5nQEaJ9wGr8TNSBeR5ojYU8YTBmqelyVENnEr3JTCQlbwsjRFlZ2E8Np+2QSa/ae3NsgejuMW16RlRe04XqmU9DpepFbRqalrocZZ9Pfeqm/
*/

window.$$crypt = (function(){
  let MAIN = function(main){
    if(!main){return;}
    main.$$crypt = this;
  };

  MAIN.prototype.set_key = function(key){
    if(typeof key === "number"){
      key = String(key);
    }
    let res = 0;
    for (let i = 0; i < key.length; i++) {
      res += key.charCodeAt(i);
    }
    return res;
  };

  MAIN.prototype.encode = function(value , ver) {
    switch(ver){
      case 1:
        return "1:"+ this.encode_code(value);
      case 2:
        return "2:"+ this.encode_bin(value);
      case 3:
        return "3:"+ this.encode_decimal(value);
      case 4:
        return "4:"+ this.encode_norm(value);
      case 5:
        return "5:"+ this.compress(value);
      default:
        return value;
    }
    // return this.compress(value);
  };
  MAIN.prototype.decode = function(val){
    if(!val){return null;}
    // val = val.trim();
// console.log(val);
    // let reg = /^(\d+?):(.+?)$/g;
    let datas = val.match(/^(\d+?):(.+?)$/);
    if(!datas){return null;}
    // let reg = new RegExp('^(\d+?):(.+?)$');
    // let datas = reg.exec(val);
// console.log(datas);

    // datas = [datas[0] , Number(datas[1]) , datas[2]] || [datas , null , val];
    // datas = [datas[0] , Number(datas[1]) , datas[2]];

    switch(datas[1]){
      case "1":
        return this.decode_code(datas[2]);
      case "2":
        return this.decode_bin(datas[2]);
      case "3":
        return this.decode_decimal(datas[2]);
      case "4":
        return this.decode_norm(datas[2]);
      case "5":
        return this.restore(datas[2]);
      default:
        return val;
    }
  };

  // << ノーマライズ処理
  /* 4 : 暗号化 */
  MAIN.prototype.encode_norm = function(val) {
    let key = this.key !== undefined ? this.key : 0;
    val = encodeURI(val);
    let arr = [];
    let digits = 0;
    for (let i = 0; i < val.length; i++) {
      let num = val.charCodeAt(i) + key;
      let hex100 = this.decimal_encode(num);
      arr.push(hex100);
      digits = digits < hex100.length ? hex100.length : digits;
    }
    for(let a of arr){
      a = ("0".repeat(digits) + a).slice(-digits);
    }
    return digits+":"+btoa(arr.join(""));
  };

  /* 復号化 */
  MAIN.prototype.decode_norm= function(enc_val) {
    let key = this.key !== undefined ? this.key : 0;
    let sp = enc_val.split(":");
    let digit = sp[1];
    let val = atob(sp[sp.length-1]);
    let reg = new RegExp(".{"+digit+"}" , "g");
    let arr = val.match(reg);
    let res = "";
    for (let a of arr) {
      res += String.fromCharCode(this.decimal_decode(a) - key);
    }
    return decodeURI(res);
  };
  // バイナリコード・パターン >>

  

  // << 100進数・パターン
  /* 3 : 暗号化 */
  MAIN.prototype.encode_decimal = function(val) {
    let key = this.key !== undefined ? this.key : 0;
    val = encodeURIComponent(val);
    let str = "";
    for (let i = 0; i < val.length; i++) {
      let conv = this.decimal_encode(val.charCodeAt(i) + key);
      str += ("00"+conv).slice(-2);
    }
    // console.log(str);
    // return str;
    return btoa(str);
  };

  /* 復号化 */
  MAIN.prototype.decode_decimal = function(enc_val) {
    if(enc_val === "" || enc_val === undefined || enc_val === null){return "";}
    let key = this.key !== undefined ? this.key : 0;
    enc_val = enc_val.replace(/^3\:/,"");
    let arr = atob(enc_val).match(/.{2}/g);
    let res = "";
    for (let a of arr) {
      res += String.fromCharCode(this.decimal_decode(a) - key);
    }
    return decodeURIComponent(res);
  };
  // バイナリコード・パターン >>


  // << 16進数・パターン
  /* 2 : 暗号化 */
  MAIN.prototype.delimitor_bin = ":";
  MAIN.prototype.encode_bin = function(val) {
    let key = this.key !== undefined ? this.key : 0;
    val = encodeURIComponent(val);
    let arr = [];
    for (let i = 0; i < val.length; i++) {
      let hex = (val.charCodeAt(i) + key).toString(16);
      arr.push(hex);
    }
    return btoa(arr.join(this.delimitor_bin));
  };

  /* 復号化 */
  MAIN.prototype.decode_bin = function(enc_val) {
    let key = this.key !== undefined ? this.key : 0;
    enc_val = enc_val.replace(/^2\:/,"");
    let arr = atob(enc_val).split(this.delimitor_bin);
    let res = "";
    for (let a of arr) {
      res += String.fromCharCode(parseInt(a,16) - key);
    }
    return decodeURIComponent(res);
  };
  // バイナリコード・パターン >>



  // << 文字列変換パターン
  /* 1 : 暗号化 */
  MAIN.prototype.encode_code = function(val) {
    let key = this.key !== undefined ? this.key : 0;

    val = encodeURIComponent(val);
    let res = "";
    for (let i = 0; i < val.length; i++) {
      res += String.fromCharCode(val.charCodeAt(i) + key);
    }
    return btoa(encodeURIComponent(res));
  };

  /* 復号化 */
  MAIN.prototype.decode_code = function(enc_val) {
    let key = this.key !== undefined ? this.key : 0;
    enc_val = enc_val.replace(/^1\:/,"");
    let val = decodeURIComponent(atob(enc_val));
    let res = "";
    for (let i = 0; i < val.length; i++) {
      res += String.fromCharCode(val.charCodeAt(i) - key);
    }
    return decodeURIComponent(res);
  };
  // 文字列変換パターン >>


  // Lib
  /**
	* 62進数
	**/
	MAIN.prototype.decimal_chars = function(){
    if(this.chars === undefined){
      var chars = "0123456789";
      chars += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      chars += "abcdefghijklmnopqrstuvwxyz";
      chars += "!\"#$%&'()*+,-./";
      // chars += "!#$%&'()*+,-./";
      chars += ";<=>?@";
      chars += "[\\]^_`";
      // chars += "[]^_`";
      chars += "{|}~";
      chars += "¥¦§¨©ª®";
      this.chars = chars;
    }
    return this.chars;
  };
	MAIN.prototype.decimal_encode = function(num){
    var chars = this.decimal_chars();
    var cn = chars.length;
    var str = [];
    var a1 , a2;
    while (num != 0) {
      a1 = parseInt(num / cn);
      a2 = num - (a1 * cn);
      str.unshift(chars.substr(a2,1));
      num = a1;
    }
    var res = str.join("");
    res = (!res)?"0":res;
    return res;
  };
  MAIN.prototype.decimal_decode = function(num){
    var chars = this.decimal_chars();
    var char2 = {};
    var cn = chars.length;
    for (var i=0; i< cn; i++) {
      char2[chars[i]] = i;
    }
    var str = 0;
    for (var i=0; i<num.toString().length; i++) {
      str += char2[num.substr((i+1)*-1, 1)] * Math.pow(cn, i);
    }
    return str;
  };

  // Lib
   /**
	* deflate , infralte
	**/
  // RawDeflate : 文字列の短縮圧縮
  MAIN.prototype.compress = function(val){
    val = encodeURIComponent(val); // UTF16 → UTF8
    val = RawDeflate.deflate(val); // 圧縮
    val = btoa(val); // base64エンコード
    return val;
  };
  // RawDeflate : 短縮圧縮した文字列の復元
  MAIN.prototype.restore = function(val){
    val = atob(val); // base64デコード
    val = RawDeflate.inflate(val); // 復号
    val = decodeURIComponent(val); // UTF8 → UTF16
    return val;
  };

  // string -> base64
  MAIN.prototype.s2b = function(str){
    return btoa(unescape(encodeURIComponent(str)));
  };
  // base64 -> string
  MAIN.prototype.b2s = function(base64){
    return decodeURIComponent(escape(atob(base64)));
  };

  return MAIN;
})();

// rowdeflate.js
(function(oa){var Q,m,ea,pa,R=null,u,n,fa,f,qa,ra,A,B,w,C,D,P,Ca,S,l,x,g,W,H,k,Da,sa,T,Ea,E,I,J,U,s,y,z,K,t,q,L,X,F,$,V,ta,ga,ha,M,ia,ua,Y,aa,N,Z,ja,va,ba=function(){this.dl=this.fc=0},wa=function(){this.extra_bits=this.static_tree=this.dyn_tree=null;this.max_code=this.max_length=this.elems=this.extra_base=0},G=function(c,e,b,a){this.good_length=c;this.max_lazy=e;this.nice_length=b;this.max_chain=a},Sa=function(){this.next=null;this.len=0;this.ptr=Array(8192);this.off=0},xa=[0,0,0,0,0,0,0,0,1,1,1,
1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0],ca=[0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13],Ta=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,2,3,7],Fa=[16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15],ya=[new G(0,0,0,0),new G(4,4,8,4),new G(4,5,16,8),new G(4,6,32,32),new G(4,4,16,16),new G(8,16,32,32),new G(8,16,128,128),new G(8,32,128,256),new G(32,128,258,1024),new G(32,258,258,4096)],ka=function(c){R[n+u++]=c;if(8192==n+u&&0!=u){var e;null!=Q?(c=Q,Q=Q.next):c=new Sa;c.next=null;c.len=c.off=
0;null==m?m=ea=c:ea=ea.next=c;c.len=u-n;for(e=0;e<c.len;e++)c.ptr[e]=R[n+e];u=n=0}},la=function(c){c&=65535;8190>n+u?(R[n+u++]=c&255,R[n+u++]=c>>>8):(ka(c&255),ka(c>>>8))},ma=function(){D=(D<<5^f[g+3-1]&255)&8191;P=A[32768+D];A[g&32767]=P;A[32768+D]=g},O=function(c,e){v(e[c].fc,e[c].dl)},Ga=function(c,e,b){return c[e].fc<c[b].fc||c[e].fc==c[b].fc&&F[e]<=F[b]},Ha=function(c,e,b){var a;for(a=0;a<b&&va<ja.length;a++)c[e+a]=ja.charCodeAt(va++)&255;return a},Ja=function(c){var e=Da,b=g,a,d=x,h=32506<g?
g-32506:0,r=g+258,Ia=f[b+d-1],k=f[b+d];x>=Ea&&(e>>=2);do if(a=c,f[a+d]==k&&f[a+d-1]==Ia&&f[a]==f[b]&&f[++a]==f[b+1]){b+=2;a++;do;while(f[++b]==f[++a]&&f[++b]==f[++a]&&f[++b]==f[++a]&&f[++b]==f[++a]&&f[++b]==f[++a]&&f[++b]==f[++a]&&f[++b]==f[++a]&&f[++b]==f[++a]&&b<r);a=258-(r-b);b=r-258;if(a>d){W=c;d=a;if(258<=a)break;Ia=f[b+d-1];k=f[b+d]}}while((c=A[c&32767])>h&&0!=--e);return d},za=function(){var c,e,b=65536-k-g;if(-1==b)b--;else if(65274<=g){for(c=0;32768>c;c++)f[c]=f[c+32768];W-=32768;g-=32768;
C-=32768;for(c=0;8192>c;c++)e=A[32768+c],A[32768+c]=32768<=e?e-32768:0;for(c=0;32768>c;c++)e=A[c],A[c]=32768<=e?e-32768:0;b+=32768}H||(c=Ha(f,g+k,b),0>=c?H=!0:k+=c)},Ua=function(c,e,b){var a;if(!pa){if(!H){w=B=0;var d,h;if(0==U[0].dl){y.dyn_tree=E;y.static_tree=J;y.extra_bits=xa;y.extra_base=257;y.elems=286;y.max_length=15;y.max_code=0;z.dyn_tree=I;z.static_tree=U;z.extra_bits=ca;z.extra_base=0;z.elems=30;z.max_length=15;z.max_code=0;K.dyn_tree=s;K.static_tree=null;K.extra_bits=Ta;K.extra_base=0;
K.elems=19;K.max_length=7;for(h=d=K.max_code=0;28>h;h++)for(ta[h]=d,a=0;a<1<<xa[h];a++)$[d++]=h;$[d-1]=h;for(h=d=0;16>h;h++)for(ga[h]=d,a=0;a<1<<ca[h];a++)V[d++]=h;for(d>>=7;30>h;h++)for(ga[h]=d<<7,a=0;a<1<<ca[h]-7;a++)V[256+d++]=h;for(a=0;15>=a;a++)t[a]=0;for(a=0;143>=a;)J[a++].dl=8,t[8]++;for(;255>=a;)J[a++].dl=9,t[9]++;for(;279>=a;)J[a++].dl=7,t[7]++;for(;287>=a;)J[a++].dl=8,t[8]++;Ka(J,287);for(a=0;30>a;a++)U[a].dl=5,U[a].fc=La(a,5);Ma()}for(a=0;8192>a;a++)A[32768+a]=0;sa=ya[T].max_lazy;Ea=ya[T].good_length;
Da=ya[T].max_chain;C=g=0;k=Ha(f,0,65536);if(0>=k)H=!0,k=0;else{for(H=!1;262>k&&!H;)za();for(a=D=0;2>a;a++)D=(D<<5^f[a]&255)&8191}m=null;S=n=u=0;3>=T?(x=2,l=0):(l=2,S=S=0);fa=!1}pa=!0;if(0==k)return fa=!0,0}if((a=Na(c,e,b))==b)return b;if(fa)return a;if(3>=T)for(;0!=k&&null==m;){ma();0!=P&&32506>=g-P&&(l=Ja(P),l>k&&(l=k));if(3<=l)if(h=da(g-W,l-3),k-=l,l<=sa){l--;do g++,ma();while(0!=--l);g++}else g+=l,l=0,D=f[g]&255,D=(D<<5^f[g+1]&255)&8191;else h=da(0,f[g]&255),k--,g++;h&&(na(0),C=g);for(;262>k&&
!H;)za()}else for(;0!=k&&null==m;){ma();x=l;Ca=W;l=2;0!=P&&x<sa&&32506>=g-P&&(l=Ja(P),l>k&&(l=k),3==l&&4096<g-W&&l--);if(3<=x&&l<=x){h=da(g-1-Ca,x-3);k-=x-1;x-=2;do g++,ma();while(0!=--x);S=0;l=2;g++;h&&(na(0),C=g)}else 0!=S?da(0,f[g-1]&255)&&(na(0),C=g):S=1,g++,k--;for(;262>k&&!H;)za()}0==k&&(0!=S&&da(0,f[g-1]&255),na(1),fa=!0);return a+Na(c,a+e,b-a)},Na=function(c,e,b){var a,d,h;for(a=0;null!=m&&a<b;){d=b-a;d>m.len&&(d=m.len);for(h=0;h<d;h++)c[e+a+h]=m.ptr[m.off+h];m.off+=d;m.len-=d;a+=d;0==m.len&&
(d=m,m=m.next,d.next=Q,Q=d)}if(a==b)return a;if(n<u){d=b-a;d>u-n&&(d=u-n);for(h=0;h<d;h++)c[e+a+h]=R[n+h];n+=d;a+=d;u==n&&(u=n=0)}return a},Ma=function(){var c;for(c=0;286>c;c++)E[c].fc=0;for(c=0;30>c;c++)I[c].fc=0;for(c=0;19>c;c++)s[c].fc=0;E[256].fc=1;Y=M=ia=ua=N=Z=0;aa=1},Aa=function(c,e){for(var b=q[e],a=e<<1;a<=L;){a<L&&Ga(c,q[a+1],q[a])&&a++;if(Ga(c,b,q[a]))break;q[e]=q[a];e=a;a<<=1}q[e]=b},Ka=function(c,e){var b=Array(16),a=0,d;for(d=1;15>=d;d++)a=a+t[d-1]<<1,b[d]=a;for(a=0;a<=e;a++)d=c[a].dl,
0!=d&&(c[a].fc=La(b[d]++,d))},Ba=function(c){var e=c.dyn_tree,b=c.static_tree,a=c.elems,d,h=-1,r=a;L=0;X=573;for(d=0;d<a;d++)0!=e[d].fc?(q[++L]=h=d,F[d]=0):e[d].dl=0;for(;2>L;)d=q[++L]=2>h?++h:0,e[d].fc=1,F[d]=0,N--,null!=b&&(Z-=b[d].dl);c.max_code=h;for(d=L>>1;1<=d;d--)Aa(e,d);do d=q[1],q[1]=q[L--],Aa(e,1),b=q[1],q[--X]=d,q[--X]=b,e[r].fc=e[d].fc+e[b].fc,F[r]=F[d]>F[b]+1?F[d]:F[b]+1,e[d].dl=e[b].dl=r,q[1]=r++,Aa(e,1);while(2<=L);q[--X]=q[1];r=c.dyn_tree;d=c.extra_bits;var a=c.extra_base,b=c.max_code,
f=c.max_length,g=c.static_tree,k,p,l,m,n=0;for(p=0;15>=p;p++)t[p]=0;r[q[X]].dl=0;for(c=X+1;573>c;c++)k=q[c],p=r[r[k].dl].dl+1,p>f&&(p=f,n++),r[k].dl=p,k>b||(t[p]++,l=0,k>=a&&(l=d[k-a]),m=r[k].fc,N+=m*(p+l),null!=g&&(Z+=m*(g[k].dl+l)));if(0!=n){do{for(p=f-1;0==t[p];)p--;t[p]--;t[p+1]+=2;t[f]--;n-=2}while(0<n);for(p=f;0!=p;p--)for(k=t[p];0!=k;)d=q[--c],d>b||(r[d].dl!=p&&(N+=(p-r[d].dl)*r[d].fc,r[d].fc=p),k--)}Ka(e,h)},Oa=function(c,e){var b,a=-1,d,h=c[0].dl,f=0,g=7,k=4;0==h&&(g=138,k=3);c[e+1].dl=65535;
for(b=0;b<=e;b++)d=h,h=c[b+1].dl,++f<g&&d==h||(f<k?s[d].fc+=f:0!=d?(d!=a&&s[d].fc++,s[16].fc++):10>=f?s[17].fc++:s[18].fc++,f=0,a=d,0==h?(g=138,k=3):d==h?(g=6,k=3):(g=7,k=4))},Pa=function(c,e){var b,a=-1,d,h=c[0].dl,f=0,g=7,k=4;0==h&&(g=138,k=3);for(b=0;b<=e;b++)if(d=h,h=c[b+1].dl,!(++f<g&&d==h)){if(f<k){do O(d,s);while(0!=--f)}else 0!=d?(d!=a&&(O(d,s),f--),O(16,s),v(f-3,2)):10>=f?(O(17,s),v(f-3,3)):(O(18,s),v(f-11,7));f=0;a=d;0==h?(g=138,k=3):d==h?(g=6,k=3):(g=7,k=4)}},na=function(c){var e,b,a,d;
d=g-C;ha[ua]=Y;Ba(y);Ba(z);Oa(E,y.max_code);Oa(I,z.max_code);Ba(K);for(a=18;3<=a&&0==s[Fa[a]].dl;a--);N+=3*(a+1)+14;e=N+3+7>>3;b=Z+3+7>>3;b<=e&&(e=b);if(d+4<=e&&0<=C)for(v(0+c,3),Qa(),la(d),la(~d),a=0;a<d;a++)ka(f[C+a]);else if(b==e)v(2+c,3),Ra(J,U);else{v(4+c,3);d=y.max_code+1;e=z.max_code+1;a+=1;v(d-257,5);v(e-1,5);v(a-4,4);for(b=0;b<a;b++)v(s[Fa[b]].dl,3);Pa(E,d-1);Pa(I,e-1);Ra(E,I)}Ma();0!=c&&Qa()},da=function(c,e){ra[M++]=e;0==c?E[e].fc++:(c--,E[$[e]+256+1].fc++,I[(256>c?V[c]:V[256+(c>>7)])&
255].fc++,qa[ia++]=c,Y|=aa);aa<<=1;0==(M&7)&&(ha[ua++]=Y,Y=0,aa=1);if(2<T&&0==(M&4095)){var b=8*M,a=g-C,d;for(d=0;30>d;d++)b+=I[d].fc*(5+ca[d]);b>>=3;if(ia<parseInt(M/2)&&b<parseInt(a/2))return!0}return 8191==M||8192==ia},Ra=function(c,e){var b,a=0,d=0,h=0,f=0,g,k;if(0!=M){do 0==(a&7)&&(f=ha[h++]),b=ra[a++]&255,0==(f&1)?O(b,c):(g=$[b],O(g+256+1,c),k=xa[g],0!=k&&(b-=ta[g],v(b,k)),b=qa[d++],g=(256>b?V[b]:V[256+(b>>7)])&255,O(g,e),k=ca[g],0!=k&&(b-=ga[g],v(b,k))),f>>=1;while(a<M)}O(256,c)},v=function(c,
e){w>16-e?(B|=c<<w,la(B),B=c>>16-w,w+=e-16):(B|=c<<w,w+=e)},La=function(c,e){var b=0;do b|=c&1,c>>=1,b<<=1;while(0<--e);return b>>1},Qa=function(){8<w?la(B):0<w&&ka(B);w=B=0};oa.RawDeflate||(oa.RawDeflate={});oa.RawDeflate.deflate=function(c,e){var b,a;ja=c;va=0;"undefined"==typeof e&&(e=6);(b=e)?1>b?b=1:9<b&&(b=9):b=6;T=b;H=pa=!1;if(null==R){Q=m=ea=null;R=Array(8192);f=Array(65536);qa=Array(8192);ra=Array(32832);A=Array(65536);E=Array(573);for(b=0;573>b;b++)E[b]=new ba;I=Array(61);for(b=0;61>b;b++)I[b]=
new ba;J=Array(288);for(b=0;288>b;b++)J[b]=new ba;U=Array(30);for(b=0;30>b;b++)U[b]=new ba;s=Array(39);for(b=0;39>b;b++)s[b]=new ba;y=new wa;z=new wa;K=new wa;t=Array(16);q=Array(573);F=Array(573);$=Array(256);V=Array(512);ta=Array(29);ga=Array(30);ha=Array(1024)}for(var d=Array(1024),h=[];0<(b=Ua(d,0,d.length));){var g=Array(b);for(a=0;a<b;a++)g[a]=String.fromCharCode(d[a]);h[h.length]=g.join("")}ja=null;return h.join("")}})(this);

// rowinflate.js
(function(L){var p,f,H=null,O,I,D,A,v,F,l,C,w,M,s,E,J,N,W=[0,1,3,7,15,31,63,127,255,511,1023,2047,4095,8191,16383,32767,65535],P=[3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258,0,0],Q=[0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0,99,99],R=[1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577],S=[0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13],T=[16,17,18,0,8,7,9,6,10,
5,11,4,12,3,13,2,14,1,15],U=function(){this.list=this.next=null},V=function(){this.n=this.b=this.e=0;this.t=null},G=function(t,x,u,b,a,q){this.BMAX=16;this.N_MAX=288;this.status=0;this.root=null;this.m=0;var r=Array(this.BMAX+1),n,c,m,k,d,e,g,h=Array(this.BMAX+1),f,l,B,y=new V,s=Array(this.BMAX);k=Array(this.N_MAX);var z,p=Array(this.BMAX+1),v,w,A;A=this.root=null;for(d=0;d<r.length;d++)r[d]=0;for(d=0;d<h.length;d++)h[d]=0;for(d=0;d<s.length;d++)s[d]=null;for(d=0;d<k.length;d++)k[d]=0;for(d=0;d<p.length;d++)p[d]=
0;n=256<x?t[256]:this.BMAX;f=t;l=0;d=x;do r[f[l]]++,l++;while(0<--d);if(r[0]==x)this.root=null,this.status=this.m=0;else{for(e=1;e<=this.BMAX&&0==r[e];e++);g=e;q<e&&(q=e);for(d=this.BMAX;0!=d&&0==r[d];d--);m=d;q>d&&(q=d);for(v=1<<e;e<d;e++,v<<=1)if(0>(v-=r[e])){this.status=2;this.m=q;return}if(0>(v-=r[d]))this.status=2,this.m=q;else{r[d]+=v;p[1]=e=0;f=r;l=1;for(B=2;0<--d;)p[B++]=e+=f[l++];f=t;d=l=0;do 0!=(e=f[l++])&&(k[p[e]++]=d);while(++d<x);x=p[m];p[0]=d=0;f=k;l=0;k=-1;z=h[0]=0;B=null;for(w=0;g<=
m;g++)for(t=r[g];0<t--;){for(;g>z+h[1+k];){z+=h[1+k];k++;w=(w=m-z)>q?q:w;if((c=1<<(e=g-z))>t+1)for(c-=t+1,B=g;++e<w&&!((c<<=1)<=r[++B]);)c-=r[B];z+e>n&&z<n&&(e=n-z);w=1<<e;h[1+k]=e;B=Array(w);for(c=0;c<w;c++)B[c]=new V;A=null==A?this.root=new U:A.next=new U;A.next=null;A.list=B;s[k]=B;0<k&&(p[k]=d,y.b=h[k],y.e=16+e,y.t=B,e=(d&(1<<z)-1)>>z-h[k],s[k-1][e].e=y.e,s[k-1][e].b=y.b,s[k-1][e].n=y.n,s[k-1][e].t=y.t)}y.b=g-z;l>=x?y.e=99:f[l]<u?(y.e=256>f[l]?16:15,y.n=f[l++]):(y.e=a[f[l]-u],y.n=b[f[l++]-u]);
c=1<<g-z;for(e=d>>z;e<w;e+=c)B[e].e=y.e,B[e].b=y.b,B[e].n=y.n,B[e].t=y.t;for(e=1<<g-1;0!=(d&e);e>>=1)d^=e;for(d^=e;(d&(1<<z)-1)!=p[k];)z-=h[k],k--}this.m=h[1];this.status=0!=v&&1!=m?1:0}}},g=function(t){for(;A<t;){var g=D,f;f=J.length==N?-1:J.charCodeAt(N++)&255;D=g|f<<A;A+=8}},h=function(f){return D&W[f]},m=function(f){D>>=f;A-=f},K=function(t,x,u){var b,a,q;if(0==u)return 0;for(q=0;;){g(s);a=w.list[h(s)];for(b=a.e;16<b;){if(99==b)return-1;m(a.b);b-=16;g(b);a=a.t[h(b)];b=a.e}m(a.b);if(16==b)f&=32767,
t[x+q++]=p[f++]=a.n;else{if(15==b)break;g(b);l=a.n+h(b);m(b);g(E);a=M.list[h(E)];for(b=a.e;16<b;){if(99==b)return-1;m(a.b);b-=16;g(b);a=a.t[h(b)];b=a.e}m(a.b);g(b);C=f-a.n-h(b);for(m(b);0<l&&q<u;)l--,C&=32767,f&=32767,t[x+q++]=p[f++]=p[C++]}if(q==u)return u}v=-1;return q},X=function(f,l,u){var b,a,q,r,n,c,p,k=Array(316);for(b=0;b<k.length;b++)k[b]=0;g(5);c=257+h(5);m(5);g(5);p=1+h(5);m(5);g(4);b=4+h(4);m(4);if(286<c||30<p)return-1;for(a=0;a<b;a++)g(3),k[T[a]]=h(3),m(3);for(;19>a;a++)k[T[a]]=0;s=7;
a=new G(k,19,19,null,null,s);if(0!=a.status)return-1;w=a.root;s=a.m;r=c+p;for(b=q=0;b<r;)if(g(s),n=w.list[h(s)],a=n.b,m(a),a=n.n,16>a)k[b++]=q=a;else if(16==a){g(2);a=3+h(2);m(2);if(b+a>r)return-1;for(;0<a--;)k[b++]=q}else{17==a?(g(3),a=3+h(3),m(3)):(g(7),a=11+h(7),m(7));if(b+a>r)return-1;for(;0<a--;)k[b++]=0;q=0}s=9;a=new G(k,c,257,P,Q,s);0==s&&(a.status=1);if(0!=a.status)return-1;w=a.root;s=a.m;for(b=0;b<p;b++)k[b]=k[b+c];E=6;a=new G(k,p,0,R,S,E);M=a.root;E=a.m;return 0==E&&257<c||0!=a.status?-1:
K(f,l,u)},Y=function(t,x,u){var b,a;for(b=0;b<u&&(!F||-1!=v);){if(0<l){if(0!=v)for(;0<l&&b<u;)l--,C&=32767,f&=32767,t[x+b++]=p[f++]=p[C++];else{for(;0<l&&b<u;)l--,f&=32767,g(8),t[x+b++]=p[f++]=h(8),m(8);0==l&&(v=-1)}if(b==u)break}if(-1==v){if(F)break;g(1);0!=h(1)&&(F=!0);m(1);g(2);v=h(2);m(2);w=null;l=0}switch(v){case 0:a=t;var q=x+b,r=u-b,n=void 0,n=A&7;m(n);g(16);n=h(16);m(16);g(16);if(n!=(~D&65535))a=-1;else{m(16);l=n;for(n=0;0<l&&n<r;)l--,f&=32767,g(8),a[q+n++]=p[f++]=h(8),m(8);0==l&&(v=-1);a=
n}break;case 1:if(null!=w)a=K(t,x+b,u-b);else a:{a=t;q=x+b;r=u-b;if(null==H){for(var c=void 0,n=Array(288),c=0;144>c;c++)n[c]=8;for(;256>c;c++)n[c]=9;for(;280>c;c++)n[c]=7;for(;288>c;c++)n[c]=8;I=7;c=new G(n,288,257,P,Q,I);if(0!=c.status){alert("HufBuild error: "+c.status);a=-1;break a}H=c.root;I=c.m;for(c=0;30>c;c++)n[c]=5;zip_fixed_bd=5;c=new G(n,30,0,R,S,zip_fixed_bd);if(1<c.status){H=null;alert("HufBuild error: "+c.status);a=-1;break a}O=c.root;zip_fixed_bd=c.m}w=H;M=O;s=I;E=zip_fixed_bd;a=K(a,
q,r)}break;case 2:a=null!=w?K(t,x+b,u-b):X(t,x+b,u-b);break;default:a=-1}if(-1==a)return F?0:-1;b+=a}return b};L.RawDeflate||(L.RawDeflate={});L.RawDeflate.inflate=function(g){var h;null==p&&(p=Array(65536));A=D=f=0;v=-1;F=!1;l=C=0;w=null;J=g;N=0;for(var m=Array(1024),b=[];0<(g=Y(m,0,m.length));){var a=Array(g);for(h=0;h<g;h++)a[h]=String.fromCharCode(m[h]);b[b.length]=a.join("")}J=null;return b.join("")}})(this);
