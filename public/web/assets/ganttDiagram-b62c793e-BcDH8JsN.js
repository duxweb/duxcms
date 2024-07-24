import{d as it,s as se,g as ie,I as ne,J as re,c as ae,b as oe,K as ce,o as le,l as yt,i as ht,R as ue,S as de,T as fe,j as he,U as me,V as ke,W as ye,X as Wt,Y as Vt,Z as Ot,$ as Pt,a0 as zt,a1 as Rt,a2 as Nt,a3 as ge,k as pe,E as ve,a4 as xe,a5 as Te,a6 as be,a7 as we,a8 as _e,a9 as De,aa as Ce}from"./vendor-markdown-CL7ixDl-.js";import{d as N}from"./vendor-tdesign-BxQoi5Mo.js";import{c as wt,g as _t}from"./vendor-react-BlTfyhut.js";import"./vendor-refine-BK41FXO6.js";var Ht={exports:{}};(function(t,n){(function(i,r){t.exports=r()})(wt,function(){var i="day";return function(r,a,m){var h=function(x){return x.add(4-x.isoWeekday(),i)},T=a.prototype;T.isoWeekYear=function(){return h(this).year()},T.isoWeek=function(x){if(!this.$utils().u(x))return this.add(7*(x-this.isoWeek()),i);var D,S,W,P,z=h(this),f=(D=this.isoWeekYear(),S=this.$u,W=(S?m.utc:m)().year(D).startOf("year"),P=4-W.isoWeekday(),W.isoWeekday()>4&&(P+=7),W.add(P,i));return z.diff(f,"week")+1},T.isoWeekday=function(x){return this.$utils().u(x)?this.day()||7:this.day(this.day()%7?x:x-7)};var F=T.startOf;T.startOf=function(x,D){var S=this.$utils(),W=!!S.u(D)||D;return S.p(x)==="isoweek"?W?this.date(this.date()-(this.isoWeekday()-1)).startOf("day"):this.date(this.date()-1-(this.isoWeekday()-1)+7).endOf("day"):F.bind(this)(x,D)}}})})(Ht);var Se=Ht.exports;const Ee=_t(Se);var Xt={exports:{}};(function(t,n){(function(i,r){t.exports=r()})(wt,function(){var i={LTS:"h:mm:ss A",LT:"h:mm A",L:"MM/DD/YYYY",LL:"MMMM D, YYYY",LLL:"MMMM D, YYYY h:mm A",LLLL:"dddd, MMMM D, YYYY h:mm A"},r=/(\[[^[]*\])|([-_:/.,()\s]+)|(A|a|YYYY|YY?|MM?M?M?|Do|DD?|hh?|HH?|mm?|ss?|S{1,3}|z|ZZ?)/g,a=/\d\d/,m=/\d\d?/,h=/\d*[^-_:/,()\s\d]+/,T={},F=function(f){return(f=+f)+(f>68?1900:2e3)},x=function(f){return function(_){this[f]=+_}},D=[/[+-]\d\d:?(\d\d)?|Z/,function(f){(this.zone||(this.zone={})).offset=function(_){if(!_||_==="Z")return 0;var A=_.match(/([+-]|\d\d)/g),L=60*A[1]+(+A[2]||0);return L===0?0:A[0]==="+"?-L:L}(f)}],S=function(f){var _=T[f];return _&&(_.indexOf?_:_.s.concat(_.f))},W=function(f,_){var A,L=T.meridiem;if(L){for(var B=1;B<=24;B+=1)if(f.indexOf(L(B,0,_))>-1){A=B>12;break}}else A=f===(_?"pm":"PM");return A},P={A:[h,function(f){this.afternoon=W(f,!1)}],a:[h,function(f){this.afternoon=W(f,!0)}],S:[/\d/,function(f){this.milliseconds=100*+f}],SS:[a,function(f){this.milliseconds=10*+f}],SSS:[/\d{3}/,function(f){this.milliseconds=+f}],s:[m,x("seconds")],ss:[m,x("seconds")],m:[m,x("minutes")],mm:[m,x("minutes")],H:[m,x("hours")],h:[m,x("hours")],HH:[m,x("hours")],hh:[m,x("hours")],D:[m,x("day")],DD:[a,x("day")],Do:[h,function(f){var _=T.ordinal,A=f.match(/\d+/);if(this.day=A[0],_)for(var L=1;L<=31;L+=1)_(L).replace(/\[|\]/g,"")===f&&(this.day=L)}],M:[m,x("month")],MM:[a,x("month")],MMM:[h,function(f){var _=S("months"),A=(S("monthsShort")||_.map(function(L){return L.slice(0,3)})).indexOf(f)+1;if(A<1)throw new Error;this.month=A%12||A}],MMMM:[h,function(f){var _=S("months").indexOf(f)+1;if(_<1)throw new Error;this.month=_%12||_}],Y:[/[+-]?\d+/,x("year")],YY:[a,function(f){this.year=F(f)}],YYYY:[/\d{4}/,x("year")],Z:D,ZZ:D};function z(f){var _,A;_=f,A=T&&T.formats;for(var L=(f=_.replace(/(\[[^\]]+])|(LTS?|l{1,4}|L{1,4})/g,function(b,v,y){var s=y&&y.toUpperCase();return v||A[y]||i[y]||A[s].replace(/(\[[^\]]+])|(MMMM|MM|DD|dddd)/g,function(u,d,o){return d||o.slice(1)})})).match(r),B=L.length,G=0;G<B;G+=1){var U=L[G],X=P[U],H=X&&X[0],k=X&&X[1];L[G]=k?{regex:H,parser:k}:U.replace(/^\[|\]$/g,"")}return function(b){for(var v={},y=0,s=0;y<B;y+=1){var u=L[y];if(typeof u=="string")s+=u.length;else{var d=u.regex,o=u.parser,g=b.slice(s),e=d.exec(g)[0];o.call(v,e),b=b.replace(e,"")}}return function(I){var c=I.afternoon;if(c!==void 0){var l=I.hours;c?l<12&&(I.hours+=12):l===12&&(I.hours=0),delete I.afternoon}}(v),v}}return function(f,_,A){A.p.customParseFormat=!0,f&&f.parseTwoDigitYear&&(F=f.parseTwoDigitYear);var L=_.prototype,B=L.parse;L.parse=function(G){var U=G.date,X=G.utc,H=G.args;this.$u=X;var k=H[1];if(typeof k=="string"){var b=H[2]===!0,v=H[3]===!0,y=b||v,s=H[2];v&&(s=H[2]),T=this.$locale(),!b&&s&&(T=A.Ls[s]),this.$d=function(g,e,I){try{if(["x","X"].indexOf(e)>-1)return new Date((e==="X"?1e3:1)*g);var c=z(e)(g),l=c.year,p=c.month,Y=c.day,C=c.hours,E=c.minutes,w=c.seconds,M=c.milliseconds,tt=c.zone,Q=new Date,at=Y||(l||p?1:Q.getDate()),ot=l||Q.getFullYear(),V=0;l&&!p||(V=p>0?p-1:Q.getMonth());var j=C||0,R=E||0,et=w||0,Z=M||0;return tt?new Date(Date.UTC(ot,V,at,j,R,et,Z+60*tt.offset*1e3)):I?new Date(Date.UTC(ot,V,at,j,R,et,Z)):new Date(ot,V,at,j,R,et,Z)}catch{return new Date("")}}(U,k,X),this.init(),s&&s!==!0&&(this.$L=this.locale(s).$L),y&&U!=this.format(k)&&(this.$d=new Date("")),T={}}else if(k instanceof Array)for(var u=k.length,d=1;d<=u;d+=1){H[1]=k[d-1];var o=A.apply(this,H);if(o.isValid()){this.$d=o.$d,this.$L=o.$L,this.init();break}d===u&&(this.$d=new Date(""))}else B.call(this,G)}}})})(Xt);var Me=Xt.exports;const Ae=_t(Me);var jt={exports:{}};(function(t,n){(function(i,r){t.exports=r()})(wt,function(){return function(i,r){var a=r.prototype,m=a.format;a.format=function(h){var T=this,F=this.$locale();if(!this.isValid())return m.bind(this)(h);var x=this.$utils(),D=(h||"YYYY-MM-DDTHH:mm:ssZ").replace(/\[([^\]]+)]|Q|wo|ww|w|WW|W|zzz|z|gggg|GGGG|Do|X|x|k{1,2}|S/g,function(S){switch(S){case"Q":return Math.ceil((T.$M+1)/3);case"Do":return F.ordinal(T.$D);case"gggg":return T.weekYear();case"GGGG":return T.isoWeekYear();case"wo":return F.ordinal(T.week(),"W");case"w":case"ww":return x.s(T.week(),S==="w"?1:2,"0");case"W":case"WW":return x.s(T.isoWeek(),S==="W"?1:2,"0");case"k":case"kk":return x.s(String(T.$H===0?24:T.$H),S==="k"?1:2,"0");case"X":return Math.floor(T.$d.getTime()/1e3);case"x":return T.$d.getTime();case"z":return"["+T.offsetName()+"]";case"zzz":return"["+T.offsetName("long")+"]";default:return S}});return m.bind(this)(D)}}})})(jt);var Le=jt.exports;const Ie=_t(Le);var vt=function(){var t=function(y,s,u,d){for(u=u||{},d=y.length;d--;u[y[d]]=s);return u},n=[6,8,10,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,30,32,33,35,37],i=[1,25],r=[1,26],a=[1,27],m=[1,28],h=[1,29],T=[1,30],F=[1,31],x=[1,9],D=[1,10],S=[1,11],W=[1,12],P=[1,13],z=[1,14],f=[1,15],_=[1,16],A=[1,18],L=[1,19],B=[1,20],G=[1,21],U=[1,22],X=[1,24],H=[1,32],k={trace:function(){},yy:{},symbols_:{error:2,start:3,gantt:4,document:5,EOF:6,line:7,SPACE:8,statement:9,NL:10,weekday:11,weekday_monday:12,weekday_tuesday:13,weekday_wednesday:14,weekday_thursday:15,weekday_friday:16,weekday_saturday:17,weekday_sunday:18,dateFormat:19,inclusiveEndDates:20,topAxis:21,axisFormat:22,tickInterval:23,excludes:24,includes:25,todayMarker:26,title:27,acc_title:28,acc_title_value:29,acc_descr:30,acc_descr_value:31,acc_descr_multiline_value:32,section:33,clickStatement:34,taskTxt:35,taskData:36,click:37,callbackname:38,callbackargs:39,href:40,clickStatementDebug:41,$accept:0,$end:1},terminals_:{2:"error",4:"gantt",6:"EOF",8:"SPACE",10:"NL",12:"weekday_monday",13:"weekday_tuesday",14:"weekday_wednesday",15:"weekday_thursday",16:"weekday_friday",17:"weekday_saturday",18:"weekday_sunday",19:"dateFormat",20:"inclusiveEndDates",21:"topAxis",22:"axisFormat",23:"tickInterval",24:"excludes",25:"includes",26:"todayMarker",27:"title",28:"acc_title",29:"acc_title_value",30:"acc_descr",31:"acc_descr_value",32:"acc_descr_multiline_value",33:"section",35:"taskTxt",36:"taskData",37:"click",38:"callbackname",39:"callbackargs",40:"href"},productions_:[0,[3,3],[5,0],[5,2],[7,2],[7,1],[7,1],[7,1],[11,1],[11,1],[11,1],[11,1],[11,1],[11,1],[11,1],[9,1],[9,1],[9,1],[9,1],[9,1],[9,1],[9,1],[9,1],[9,1],[9,1],[9,2],[9,2],[9,1],[9,1],[9,1],[9,2],[34,2],[34,3],[34,3],[34,4],[34,3],[34,4],[34,2],[41,2],[41,3],[41,3],[41,4],[41,3],[41,4],[41,2]],performAction:function(s,u,d,o,g,e,I){var c=e.length-1;switch(g){case 1:return e[c-1];case 2:this.$=[];break;case 3:e[c-1].push(e[c]),this.$=e[c-1];break;case 4:case 5:this.$=e[c];break;case 6:case 7:this.$=[];break;case 8:o.setWeekday("monday");break;case 9:o.setWeekday("tuesday");break;case 10:o.setWeekday("wednesday");break;case 11:o.setWeekday("thursday");break;case 12:o.setWeekday("friday");break;case 13:o.setWeekday("saturday");break;case 14:o.setWeekday("sunday");break;case 15:o.setDateFormat(e[c].substr(11)),this.$=e[c].substr(11);break;case 16:o.enableInclusiveEndDates(),this.$=e[c].substr(18);break;case 17:o.TopAxis(),this.$=e[c].substr(8);break;case 18:o.setAxisFormat(e[c].substr(11)),this.$=e[c].substr(11);break;case 19:o.setTickInterval(e[c].substr(13)),this.$=e[c].substr(13);break;case 20:o.setExcludes(e[c].substr(9)),this.$=e[c].substr(9);break;case 21:o.setIncludes(e[c].substr(9)),this.$=e[c].substr(9);break;case 22:o.setTodayMarker(e[c].substr(12)),this.$=e[c].substr(12);break;case 24:o.setDiagramTitle(e[c].substr(6)),this.$=e[c].substr(6);break;case 25:this.$=e[c].trim(),o.setAccTitle(this.$);break;case 26:case 27:this.$=e[c].trim(),o.setAccDescription(this.$);break;case 28:o.addSection(e[c].substr(8)),this.$=e[c].substr(8);break;case 30:o.addTask(e[c-1],e[c]),this.$="task";break;case 31:this.$=e[c-1],o.setClickEvent(e[c-1],e[c],null);break;case 32:this.$=e[c-2],o.setClickEvent(e[c-2],e[c-1],e[c]);break;case 33:this.$=e[c-2],o.setClickEvent(e[c-2],e[c-1],null),o.setLink(e[c-2],e[c]);break;case 34:this.$=e[c-3],o.setClickEvent(e[c-3],e[c-2],e[c-1]),o.setLink(e[c-3],e[c]);break;case 35:this.$=e[c-2],o.setClickEvent(e[c-2],e[c],null),o.setLink(e[c-2],e[c-1]);break;case 36:this.$=e[c-3],o.setClickEvent(e[c-3],e[c-1],e[c]),o.setLink(e[c-3],e[c-2]);break;case 37:this.$=e[c-1],o.setLink(e[c-1],e[c]);break;case 38:case 44:this.$=e[c-1]+" "+e[c];break;case 39:case 40:case 42:this.$=e[c-2]+" "+e[c-1]+" "+e[c];break;case 41:case 43:this.$=e[c-3]+" "+e[c-2]+" "+e[c-1]+" "+e[c];break}},table:[{3:1,4:[1,2]},{1:[3]},t(n,[2,2],{5:3}),{6:[1,4],7:5,8:[1,6],9:7,10:[1,8],11:17,12:i,13:r,14:a,15:m,16:h,17:T,18:F,19:x,20:D,21:S,22:W,23:P,24:z,25:f,26:_,27:A,28:L,30:B,32:G,33:U,34:23,35:X,37:H},t(n,[2,7],{1:[2,1]}),t(n,[2,3]),{9:33,11:17,12:i,13:r,14:a,15:m,16:h,17:T,18:F,19:x,20:D,21:S,22:W,23:P,24:z,25:f,26:_,27:A,28:L,30:B,32:G,33:U,34:23,35:X,37:H},t(n,[2,5]),t(n,[2,6]),t(n,[2,15]),t(n,[2,16]),t(n,[2,17]),t(n,[2,18]),t(n,[2,19]),t(n,[2,20]),t(n,[2,21]),t(n,[2,22]),t(n,[2,23]),t(n,[2,24]),{29:[1,34]},{31:[1,35]},t(n,[2,27]),t(n,[2,28]),t(n,[2,29]),{36:[1,36]},t(n,[2,8]),t(n,[2,9]),t(n,[2,10]),t(n,[2,11]),t(n,[2,12]),t(n,[2,13]),t(n,[2,14]),{38:[1,37],40:[1,38]},t(n,[2,4]),t(n,[2,25]),t(n,[2,26]),t(n,[2,30]),t(n,[2,31],{39:[1,39],40:[1,40]}),t(n,[2,37],{38:[1,41]}),t(n,[2,32],{40:[1,42]}),t(n,[2,33]),t(n,[2,35],{39:[1,43]}),t(n,[2,34]),t(n,[2,36])],defaultActions:{},parseError:function(s,u){if(u.recoverable)this.trace(s);else{var d=new Error(s);throw d.hash=u,d}},parse:function(s){var u=this,d=[0],o=[],g=[null],e=[],I=this.table,c="",l=0,p=0,Y=2,C=1,E=e.slice.call(arguments,1),w=Object.create(this.lexer),M={yy:{}};for(var tt in this.yy)Object.prototype.hasOwnProperty.call(this.yy,tt)&&(M.yy[tt]=this.yy[tt]);w.setInput(s,M.yy),M.yy.lexer=w,M.yy.parser=this,typeof w.yylloc>"u"&&(w.yylloc={});var Q=w.yylloc;e.push(Q);var at=w.options&&w.options.ranges;typeof M.yy.parseError=="function"?this.parseError=M.yy.parseError:this.parseError=Object.getPrototypeOf(this).parseError;function ot(){var K;return K=o.pop()||w.lex()||C,typeof K!="number"&&(K instanceof Array&&(o=K,K=o.pop()),K=u.symbols_[K]||K),K}for(var V,j,R,et,Z={},ct,J,Ft,ft;;){if(j=d[d.length-1],this.defaultActions[j]?R=this.defaultActions[j]:((V===null||typeof V>"u")&&(V=ot()),R=I[j]&&I[j][V]),typeof R>"u"||!R.length||!R[0]){var pt="";ft=[];for(ct in I[j])this.terminals_[ct]&&ct>Y&&ft.push("'"+this.terminals_[ct]+"'");w.showPosition?pt="Parse error on line "+(l+1)+`:
`+w.showPosition()+`
Expecting `+ft.join(", ")+", got '"+(this.terminals_[V]||V)+"'":pt="Parse error on line "+(l+1)+": Unexpected "+(V==C?"end of input":"'"+(this.terminals_[V]||V)+"'"),this.parseError(pt,{text:w.match,token:this.terminals_[V]||V,line:w.yylineno,loc:Q,expected:ft})}if(R[0]instanceof Array&&R.length>1)throw new Error("Parse Error: multiple actions possible at state: "+j+", token: "+V);switch(R[0]){case 1:d.push(V),g.push(w.yytext),e.push(w.yylloc),d.push(R[1]),V=null,p=w.yyleng,c=w.yytext,l=w.yylineno,Q=w.yylloc;break;case 2:if(J=this.productions_[R[1]][1],Z.$=g[g.length-J],Z._$={first_line:e[e.length-(J||1)].first_line,last_line:e[e.length-1].last_line,first_column:e[e.length-(J||1)].first_column,last_column:e[e.length-1].last_column},at&&(Z._$.range=[e[e.length-(J||1)].range[0],e[e.length-1].range[1]]),et=this.performAction.apply(Z,[c,p,l,M.yy,R[1],g,e].concat(E)),typeof et<"u")return et;J&&(d=d.slice(0,-1*J*2),g=g.slice(0,-1*J),e=e.slice(0,-1*J)),d.push(this.productions_[R[1]][0]),g.push(Z.$),e.push(Z._$),Ft=I[d[d.length-2]][d[d.length-1]],d.push(Ft);break;case 3:return!0}}return!0}},b=function(){var y={EOF:1,parseError:function(u,d){if(this.yy.parser)this.yy.parser.parseError(u,d);else throw new Error(u)},setInput:function(s,u){return this.yy=u||this.yy||{},this._input=s,this._more=this._backtrack=this.done=!1,this.yylineno=this.yyleng=0,this.yytext=this.matched=this.match="",this.conditionStack=["INITIAL"],this.yylloc={first_line:1,first_column:0,last_line:1,last_column:0},this.options.ranges&&(this.yylloc.range=[0,0]),this.offset=0,this},input:function(){var s=this._input[0];this.yytext+=s,this.yyleng++,this.offset++,this.match+=s,this.matched+=s;var u=s.match(/(?:\r\n?|\n).*/g);return u?(this.yylineno++,this.yylloc.last_line++):this.yylloc.last_column++,this.options.ranges&&this.yylloc.range[1]++,this._input=this._input.slice(1),s},unput:function(s){var u=s.length,d=s.split(/(?:\r\n?|\n)/g);this._input=s+this._input,this.yytext=this.yytext.substr(0,this.yytext.length-u),this.offset-=u;var o=this.match.split(/(?:\r\n?|\n)/g);this.match=this.match.substr(0,this.match.length-1),this.matched=this.matched.substr(0,this.matched.length-1),d.length-1&&(this.yylineno-=d.length-1);var g=this.yylloc.range;return this.yylloc={first_line:this.yylloc.first_line,last_line:this.yylineno+1,first_column:this.yylloc.first_column,last_column:d?(d.length===o.length?this.yylloc.first_column:0)+o[o.length-d.length].length-d[0].length:this.yylloc.first_column-u},this.options.ranges&&(this.yylloc.range=[g[0],g[0]+this.yyleng-u]),this.yyleng=this.yytext.length,this},more:function(){return this._more=!0,this},reject:function(){if(this.options.backtrack_lexer)this._backtrack=!0;else return this.parseError("Lexical error on line "+(this.yylineno+1)+`. You can only invoke reject() in the lexer when the lexer is of the backtracking persuasion (options.backtrack_lexer = true).
`+this.showPosition(),{text:"",token:null,line:this.yylineno});return this},less:function(s){this.unput(this.match.slice(s))},pastInput:function(){var s=this.matched.substr(0,this.matched.length-this.match.length);return(s.length>20?"...":"")+s.substr(-20).replace(/\n/g,"")},upcomingInput:function(){var s=this.match;return s.length<20&&(s+=this._input.substr(0,20-s.length)),(s.substr(0,20)+(s.length>20?"...":"")).replace(/\n/g,"")},showPosition:function(){var s=this.pastInput(),u=new Array(s.length+1).join("-");return s+this.upcomingInput()+`
`+u+"^"},test_match:function(s,u){var d,o,g;if(this.options.backtrack_lexer&&(g={yylineno:this.yylineno,yylloc:{first_line:this.yylloc.first_line,last_line:this.last_line,first_column:this.yylloc.first_column,last_column:this.yylloc.last_column},yytext:this.yytext,match:this.match,matches:this.matches,matched:this.matched,yyleng:this.yyleng,offset:this.offset,_more:this._more,_input:this._input,yy:this.yy,conditionStack:this.conditionStack.slice(0),done:this.done},this.options.ranges&&(g.yylloc.range=this.yylloc.range.slice(0))),o=s[0].match(/(?:\r\n?|\n).*/g),o&&(this.yylineno+=o.length),this.yylloc={first_line:this.yylloc.last_line,last_line:this.yylineno+1,first_column:this.yylloc.last_column,last_column:o?o[o.length-1].length-o[o.length-1].match(/\r?\n?/)[0].length:this.yylloc.last_column+s[0].length},this.yytext+=s[0],this.match+=s[0],this.matches=s,this.yyleng=this.yytext.length,this.options.ranges&&(this.yylloc.range=[this.offset,this.offset+=this.yyleng]),this._more=!1,this._backtrack=!1,this._input=this._input.slice(s[0].length),this.matched+=s[0],d=this.performAction.call(this,this.yy,this,u,this.conditionStack[this.conditionStack.length-1]),this.done&&this._input&&(this.done=!1),d)return d;if(this._backtrack){for(var e in g)this[e]=g[e];return!1}return!1},next:function(){if(this.done)return this.EOF;this._input||(this.done=!0);var s,u,d,o;this._more||(this.yytext="",this.match="");for(var g=this._currentRules(),e=0;e<g.length;e++)if(d=this._input.match(this.rules[g[e]]),d&&(!u||d[0].length>u[0].length)){if(u=d,o=e,this.options.backtrack_lexer){if(s=this.test_match(d,g[e]),s!==!1)return s;if(this._backtrack){u=!1;continue}else return!1}else if(!this.options.flex)break}return u?(s=this.test_match(u,g[o]),s!==!1?s:!1):this._input===""?this.EOF:this.parseError("Lexical error on line "+(this.yylineno+1)+`. Unrecognized text.
`+this.showPosition(),{text:"",token:null,line:this.yylineno})},lex:function(){var u=this.next();return u||this.lex()},begin:function(u){this.conditionStack.push(u)},popState:function(){var u=this.conditionStack.length-1;return u>0?this.conditionStack.pop():this.conditionStack[0]},_currentRules:function(){return this.conditionStack.length&&this.conditionStack[this.conditionStack.length-1]?this.conditions[this.conditionStack[this.conditionStack.length-1]].rules:this.conditions.INITIAL.rules},topState:function(u){return u=this.conditionStack.length-1-Math.abs(u||0),u>=0?this.conditionStack[u]:"INITIAL"},pushState:function(u){this.begin(u)},stateStackSize:function(){return this.conditionStack.length},options:{"case-insensitive":!0},performAction:function(u,d,o,g){switch(o){case 0:return this.begin("open_directive"),"open_directive";case 1:return this.begin("acc_title"),28;case 2:return this.popState(),"acc_title_value";case 3:return this.begin("acc_descr"),30;case 4:return this.popState(),"acc_descr_value";case 5:this.begin("acc_descr_multiline");break;case 6:this.popState();break;case 7:return"acc_descr_multiline_value";case 8:break;case 9:break;case 10:break;case 11:return 10;case 12:break;case 13:break;case 14:this.begin("href");break;case 15:this.popState();break;case 16:return 40;case 17:this.begin("callbackname");break;case 18:this.popState();break;case 19:this.popState(),this.begin("callbackargs");break;case 20:return 38;case 21:this.popState();break;case 22:return 39;case 23:this.begin("click");break;case 24:this.popState();break;case 25:return 37;case 26:return 4;case 27:return 19;case 28:return 20;case 29:return 21;case 30:return 22;case 31:return 23;case 32:return 25;case 33:return 24;case 34:return 26;case 35:return 12;case 36:return 13;case 37:return 14;case 38:return 15;case 39:return 16;case 40:return 17;case 41:return 18;case 42:return"date";case 43:return 27;case 44:return"accDescription";case 45:return 33;case 46:return 35;case 47:return 36;case 48:return":";case 49:return 6;case 50:return"INVALID"}},rules:[/^(?:%%\{)/i,/^(?:accTitle\s*:\s*)/i,/^(?:(?!\n||)*[^\n]*)/i,/^(?:accDescr\s*:\s*)/i,/^(?:(?!\n||)*[^\n]*)/i,/^(?:accDescr\s*\{\s*)/i,/^(?:[\}])/i,/^(?:[^\}]*)/i,/^(?:%%(?!\{)*[^\n]*)/i,/^(?:[^\}]%%*[^\n]*)/i,/^(?:%%*[^\n]*[\n]*)/i,/^(?:[\n]+)/i,/^(?:\s+)/i,/^(?:%[^\n]*)/i,/^(?:href[\s]+["])/i,/^(?:["])/i,/^(?:[^"]*)/i,/^(?:call[\s]+)/i,/^(?:\([\s]*\))/i,/^(?:\()/i,/^(?:[^(]*)/i,/^(?:\))/i,/^(?:[^)]*)/i,/^(?:click[\s]+)/i,/^(?:[\s\n])/i,/^(?:[^\s\n]*)/i,/^(?:gantt\b)/i,/^(?:dateFormat\s[^#\n;]+)/i,/^(?:inclusiveEndDates\b)/i,/^(?:topAxis\b)/i,/^(?:axisFormat\s[^#\n;]+)/i,/^(?:tickInterval\s[^#\n;]+)/i,/^(?:includes\s[^#\n;]+)/i,/^(?:excludes\s[^#\n;]+)/i,/^(?:todayMarker\s[^\n;]+)/i,/^(?:weekday\s+monday\b)/i,/^(?:weekday\s+tuesday\b)/i,/^(?:weekday\s+wednesday\b)/i,/^(?:weekday\s+thursday\b)/i,/^(?:weekday\s+friday\b)/i,/^(?:weekday\s+saturday\b)/i,/^(?:weekday\s+sunday\b)/i,/^(?:\d\d\d\d-\d\d-\d\d\b)/i,/^(?:title\s[^\n]+)/i,/^(?:accDescription\s[^#\n;]+)/i,/^(?:section\s[^\n]+)/i,/^(?:[^:\n]+)/i,/^(?::[^#\n;]+)/i,/^(?::)/i,/^(?:$)/i,/^(?:.)/i],conditions:{acc_descr_multiline:{rules:[6,7],inclusive:!1},acc_descr:{rules:[4],inclusive:!1},acc_title:{rules:[2],inclusive:!1},callbackargs:{rules:[21,22],inclusive:!1},callbackname:{rules:[18,19,20],inclusive:!1},href:{rules:[15,16],inclusive:!1},click:{rules:[24,25],inclusive:!1},INITIAL:{rules:[0,1,3,5,8,9,10,11,12,13,14,17,23,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50],inclusive:!0}}};return y}();k.lexer=b;function v(){this.yy={}}return v.prototype=k,k.Parser=v,new v}();vt.parser=vt;const Ye=vt;N.extend(Ee);N.extend(Ae);N.extend(Ie);let q="",Dt="",Ct,St="",lt=[],ut=[],Et={},Mt=[],gt=[],rt="",At="";const Ut=["active","done","crit","milestone"];let Lt=[],dt=!1,It=!1,Yt="sunday",xt=0;const Fe=function(){Mt=[],gt=[],rt="",Lt=[],mt=0,bt=void 0,kt=void 0,O=[],q="",Dt="",At="",Ct=void 0,St="",lt=[],ut=[],dt=!1,It=!1,xt=0,Et={},ce(),Yt="sunday"},We=function(t){Dt=t},Ve=function(){return Dt},Oe=function(t){Ct=t},Pe=function(){return Ct},ze=function(t){St=t},Re=function(){return St},Ne=function(t){q=t},Be=function(){dt=!0},Ge=function(){return dt},He=function(){It=!0},Xe=function(){return It},je=function(t){At=t},Ue=function(){return At},Ze=function(){return q},qe=function(t){lt=t.toLowerCase().split(/[\s,]+/)},Qe=function(){return lt},Je=function(t){ut=t.toLowerCase().split(/[\s,]+/)},Ke=function(){return ut},$e=function(){return Et},ts=function(t){rt=t,Mt.push(t)},es=function(){return Mt},ss=function(){let t=Bt();const n=10;let i=0;for(;!t&&i<n;)t=Bt(),i++;return gt=O,gt},Zt=function(t,n,i,r){return r.includes(t.format(n.trim()))?!1:t.isoWeekday()>=6&&i.includes("weekends")||i.includes(t.format("dddd").toLowerCase())?!0:i.includes(t.format(n.trim()))},is=function(t){Yt=t},ns=function(){return Yt},qt=function(t,n,i,r){if(!i.length||t.manualEndTime)return;let a;t.startTime instanceof Date?a=N(t.startTime):a=N(t.startTime,n,!0),a=a.add(1,"d");let m;t.endTime instanceof Date?m=N(t.endTime):m=N(t.endTime,n,!0);const[h,T]=rs(a,m,n,i,r);t.endTime=h.toDate(),t.renderEndTime=T},rs=function(t,n,i,r,a){let m=!1,h=null;for(;t<=n;)m||(h=n.toDate()),m=Zt(t,i,r,a),m&&(n=n.add(1,"d")),t=t.add(1,"d");return[n,h]},Tt=function(t,n,i){i=i.trim();const a=/^after\s+(?<ids>[\d\w- ]+)/.exec(i);if(a!==null){let h=null;for(const F of a.groups.ids.split(" ")){let x=st(F);x!==void 0&&(!h||x.endTime>h.endTime)&&(h=x)}if(h)return h.endTime;const T=new Date;return T.setHours(0,0,0,0),T}let m=N(i,n.trim(),!0);if(m.isValid())return m.toDate();{yt.debug("Invalid date:"+i),yt.debug("With date format:"+n.trim());const h=new Date(i);if(h===void 0||isNaN(h.getTime())||h.getFullYear()<-1e4||h.getFullYear()>1e4)throw new Error("Invalid date:"+i);return h}},Qt=function(t){const n=/^(\d+(?:\.\d+)?)([Mdhmswy]|ms)$/.exec(t.trim());return n!==null?[Number.parseFloat(n[1]),n[2]]:[NaN,"ms"]},Jt=function(t,n,i,r=!1){i=i.trim();const m=/^until\s+(?<ids>[\d\w- ]+)/.exec(i);if(m!==null){let D=null;for(const W of m.groups.ids.split(" ")){let P=st(W);P!==void 0&&(!D||P.startTime<D.startTime)&&(D=P)}if(D)return D.startTime;const S=new Date;return S.setHours(0,0,0,0),S}let h=N(i,n.trim(),!0);if(h.isValid())return r&&(h=h.add(1,"d")),h.toDate();let T=N(t);const[F,x]=Qt(i);if(!Number.isNaN(F)){const D=T.add(F,x);D.isValid()&&(T=D)}return T.toDate()};let mt=0;const nt=function(t){return t===void 0?(mt=mt+1,"task"+mt):t},as=function(t,n){let i;n.substr(0,1)===":"?i=n.substr(1,n.length):i=n;const r=i.split(","),a={};ee(r,a,Ut);for(let h=0;h<r.length;h++)r[h]=r[h].trim();let m="";switch(r.length){case 1:a.id=nt(),a.startTime=t.endTime,m=r[0];break;case 2:a.id=nt(),a.startTime=Tt(void 0,q,r[0]),m=r[1];break;case 3:a.id=nt(r[0]),a.startTime=Tt(void 0,q,r[1]),m=r[2];break}return m&&(a.endTime=Jt(a.startTime,q,m,dt),a.manualEndTime=N(m,"YYYY-MM-DD",!0).isValid(),qt(a,q,ut,lt)),a},os=function(t,n){let i;n.substr(0,1)===":"?i=n.substr(1,n.length):i=n;const r=i.split(","),a={};ee(r,a,Ut);for(let m=0;m<r.length;m++)r[m]=r[m].trim();switch(r.length){case 1:a.id=nt(),a.startTime={type:"prevTaskEnd",id:t},a.endTime={data:r[0]};break;case 2:a.id=nt(),a.startTime={type:"getStartDate",startData:r[0]},a.endTime={data:r[1]};break;case 3:a.id=nt(r[0]),a.startTime={type:"getStartDate",startData:r[1]},a.endTime={data:r[2]};break}return a};let bt,kt,O=[];const Kt={},cs=function(t,n){const i={section:rt,type:rt,processed:!1,manualEndTime:!1,renderEndTime:null,raw:{data:n},task:t,classes:[]},r=os(kt,n);i.raw.startTime=r.startTime,i.raw.endTime=r.endTime,i.id=r.id,i.prevTaskId=kt,i.active=r.active,i.done=r.done,i.crit=r.crit,i.milestone=r.milestone,i.order=xt,xt++;const a=O.push(i);kt=i.id,Kt[i.id]=a-1},st=function(t){const n=Kt[t];return O[n]},ls=function(t,n){const i={section:rt,type:rt,description:t,task:t,classes:[]},r=as(bt,n);i.startTime=r.startTime,i.endTime=r.endTime,i.id=r.id,i.active=r.active,i.done=r.done,i.crit=r.crit,i.milestone=r.milestone,bt=i,gt.push(i)},Bt=function(){const t=function(i){const r=O[i];let a="";switch(O[i].raw.startTime.type){case"prevTaskEnd":{const m=st(r.prevTaskId);r.startTime=m.endTime;break}case"getStartDate":a=Tt(void 0,q,O[i].raw.startTime.startData),a&&(O[i].startTime=a);break}return O[i].startTime&&(O[i].endTime=Jt(O[i].startTime,q,O[i].raw.endTime.data,dt),O[i].endTime&&(O[i].processed=!0,O[i].manualEndTime=N(O[i].raw.endTime.data,"YYYY-MM-DD",!0).isValid(),qt(O[i],q,ut,lt))),O[i].processed};let n=!0;for(const[i,r]of O.entries())t(i),n=n&&r.processed;return n},us=function(t,n){let i=n;it().securityLevel!=="loose"&&(i=le.sanitizeUrl(n)),t.split(",").forEach(function(r){st(r)!==void 0&&(te(r,()=>{window.open(i,"_self")}),Et[r]=i)}),$t(t,"clickable")},$t=function(t,n){t.split(",").forEach(function(i){let r=st(i);r!==void 0&&r.classes.push(n)})},ds=function(t,n,i){if(it().securityLevel!=="loose"||n===void 0)return;let r=[];if(typeof i=="string"){r=i.split(/,(?=(?:(?:[^"]*"){2})*[^"]*$)/);for(let m=0;m<r.length;m++){let h=r[m].trim();h.charAt(0)==='"'&&h.charAt(h.length-1)==='"'&&(h=h.substr(1,h.length-2)),r[m]=h}}r.length===0&&r.push(t),st(t)!==void 0&&te(t,()=>{ve.runFunc(n,...r)})},te=function(t,n){Lt.push(function(){const i=document.querySelector(`[id="${t}"]`);i!==null&&i.addEventListener("click",function(){n()})},function(){const i=document.querySelector(`[id="${t}-text"]`);i!==null&&i.addEventListener("click",function(){n()})})},fs=function(t,n,i){t.split(",").forEach(function(r){ds(r,n,i)}),$t(t,"clickable")},hs=function(t){Lt.forEach(function(n){n(t)})},ms={getConfig:()=>it().gantt,clear:Fe,setDateFormat:Ne,getDateFormat:Ze,enableInclusiveEndDates:Be,endDatesAreInclusive:Ge,enableTopAxis:He,topAxisEnabled:Xe,setAxisFormat:We,getAxisFormat:Ve,setTickInterval:Oe,getTickInterval:Pe,setTodayMarker:ze,getTodayMarker:Re,setAccTitle:se,getAccTitle:ie,setDiagramTitle:ne,getDiagramTitle:re,setDisplayMode:je,getDisplayMode:Ue,setAccDescription:ae,getAccDescription:oe,addSection:ts,getSections:es,getTasks:ss,addTask:cs,findTaskById:st,addTaskOrg:ls,setIncludes:qe,getIncludes:Qe,setExcludes:Je,getExcludes:Ke,setClickEvent:fs,setLink:us,getLinks:$e,bindFunctions:hs,parseDuration:Qt,isInvalidDate:Zt,setWeekday:is,getWeekday:ns};function ee(t,n,i){let r=!0;for(;r;)r=!1,i.forEach(function(a){const m="^\\s*"+a+"\\s*$",h=new RegExp(m);t[0].match(h)&&(n[a]=!0,t.shift(1),r=!0)})}const ks=function(){yt.debug("Something is calling, setConf, remove the call")},Gt={monday:xe,tuesday:Te,wednesday:be,thursday:we,friday:_e,saturday:De,sunday:Ce},ys=(t,n)=>{let i=[...t].map(()=>-1/0),r=[...t].sort((m,h)=>m.startTime-h.startTime||m.order-h.order),a=0;for(const m of r)for(let h=0;h<i.length;h++)if(m.startTime>=i[h]){i[h]=m.endTime,m.order=h+n,h>a&&(a=h);break}return a};let $;const gs=function(t,n,i,r){const a=it().gantt,m=it().securityLevel;let h;m==="sandbox"&&(h=ht("#i"+n));const T=m==="sandbox"?ht(h.nodes()[0].contentDocument.body):ht("body"),F=m==="sandbox"?h.nodes()[0].contentDocument:document,x=F.getElementById(n);$=x.parentElement.offsetWidth,$===void 0&&($=1200),a.useWidth!==void 0&&($=a.useWidth);const D=r.db.getTasks();let S=[];for(const k of D)S.push(k.type);S=H(S);const W={};let P=2*a.topPadding;if(r.db.getDisplayMode()==="compact"||a.displayMode==="compact"){const k={};for(const v of D)k[v.section]===void 0?k[v.section]=[v]:k[v.section].push(v);let b=0;for(const v of Object.keys(k)){const y=ys(k[v],b)+1;b+=y,P+=y*(a.barHeight+a.barGap),W[v]=y}}else{P+=D.length*(a.barHeight+a.barGap);for(const k of S)W[k]=D.filter(b=>b.type===k).length}x.setAttribute("viewBox","0 0 "+$+" "+P);const z=T.select(`[id="${n}"]`),f=ue().domain([de(D,function(k){return k.startTime}),fe(D,function(k){return k.endTime})]).rangeRound([0,$-a.leftPadding-a.rightPadding]);function _(k,b){const v=k.startTime,y=b.startTime;let s=0;return v>y?s=1:v<y&&(s=-1),s}D.sort(_),A(D,$,P),he(z,P,$,a.useMaxWidth),z.append("text").text(r.db.getDiagramTitle()).attr("x",$/2).attr("y",a.titleTopMargin).attr("class","titleText");function A(k,b,v){const y=a.barHeight,s=y+a.barGap,u=a.topPadding,d=a.leftPadding,o=me().domain([0,S.length]).range(["#00B9FA","#F95002"]).interpolate(ke);B(s,u,d,b,v,k,r.db.getExcludes(),r.db.getIncludes()),G(d,u,b,v),L(k,s,u,d,y,o,b),U(s,u),X(d,u,b,v)}function L(k,b,v,y,s,u,d){const g=[...new Set(k.map(l=>l.order))].map(l=>k.find(p=>p.order===l));z.append("g").selectAll("rect").data(g).enter().append("rect").attr("x",0).attr("y",function(l,p){return p=l.order,p*b+v-2}).attr("width",function(){return d-a.rightPadding/2}).attr("height",b).attr("class",function(l){for(const[p,Y]of S.entries())if(l.type===Y)return"section section"+p%a.numberSectionStyles;return"section section0"});const e=z.append("g").selectAll("rect").data(k).enter(),I=r.db.getLinks();if(e.append("rect").attr("id",function(l){return l.id}).attr("rx",3).attr("ry",3).attr("x",function(l){return l.milestone?f(l.startTime)+y+.5*(f(l.endTime)-f(l.startTime))-.5*s:f(l.startTime)+y}).attr("y",function(l,p){return p=l.order,p*b+v}).attr("width",function(l){return l.milestone?s:f(l.renderEndTime||l.endTime)-f(l.startTime)}).attr("height",s).attr("transform-origin",function(l,p){return p=l.order,(f(l.startTime)+y+.5*(f(l.endTime)-f(l.startTime))).toString()+"px "+(p*b+v+.5*s).toString()+"px"}).attr("class",function(l){const p="task";let Y="";l.classes.length>0&&(Y=l.classes.join(" "));let C=0;for(const[w,M]of S.entries())l.type===M&&(C=w%a.numberSectionStyles);let E="";return l.active?l.crit?E+=" activeCrit":E=" active":l.done?l.crit?E=" doneCrit":E=" done":l.crit&&(E+=" crit"),E.length===0&&(E=" task"),l.milestone&&(E=" milestone "+E),E+=C,E+=" "+Y,p+E}),e.append("text").attr("id",function(l){return l.id+"-text"}).text(function(l){return l.task}).attr("font-size",a.fontSize).attr("x",function(l){let p=f(l.startTime),Y=f(l.renderEndTime||l.endTime);l.milestone&&(p+=.5*(f(l.endTime)-f(l.startTime))-.5*s),l.milestone&&(Y=p+s);const C=this.getBBox().width;return C>Y-p?Y+C+1.5*a.leftPadding>d?p+y-5:Y+y+5:(Y-p)/2+p+y}).attr("y",function(l,p){return p=l.order,p*b+a.barHeight/2+(a.fontSize/2-2)+v}).attr("text-height",s).attr("class",function(l){const p=f(l.startTime);let Y=f(l.endTime);l.milestone&&(Y=p+s);const C=this.getBBox().width;let E="";l.classes.length>0&&(E=l.classes.join(" "));let w=0;for(const[tt,Q]of S.entries())l.type===Q&&(w=tt%a.numberSectionStyles);let M="";return l.active&&(l.crit?M="activeCritText"+w:M="activeText"+w),l.done?l.crit?M=M+" doneCritText"+w:M=M+" doneText"+w:l.crit&&(M=M+" critText"+w),l.milestone&&(M+=" milestoneText"),C>Y-p?Y+C+1.5*a.leftPadding>d?E+" taskTextOutsideLeft taskTextOutside"+w+" "+M:E+" taskTextOutsideRight taskTextOutside"+w+" "+M+" width-"+C:E+" taskText taskText"+w+" "+M+" width-"+C}),it().securityLevel==="sandbox"){let l;l=ht("#i"+n);const p=l.nodes()[0].contentDocument;e.filter(function(Y){return I[Y.id]!==void 0}).each(function(Y){var C=p.querySelector("#"+Y.id),E=p.querySelector("#"+Y.id+"-text");const w=C.parentNode;var M=p.createElement("a");M.setAttribute("xlink:href",I[Y.id]),M.setAttribute("target","_top"),w.appendChild(M),M.appendChild(C),M.appendChild(E)})}}function B(k,b,v,y,s,u,d,o){if(d.length===0&&o.length===0)return;let g,e;for(const{startTime:C,endTime:E}of u)(g===void 0||C<g)&&(g=C),(e===void 0||E>e)&&(e=E);if(!g||!e)return;if(N(e).diff(N(g),"year")>5){yt.warn("The difference between the min and max time is more than 5 years. This will cause performance issues. Skipping drawing exclude days.");return}const I=r.db.getDateFormat(),c=[];let l=null,p=N(g);for(;p.valueOf()<=e;)r.db.isInvalidDate(p,I,d,o)?l?l.end=p:l={start:p,end:p}:l&&(c.push(l),l=null),p=p.add(1,"d");z.append("g").selectAll("rect").data(c).enter().append("rect").attr("id",function(C){return"exclude-"+C.start.format("YYYY-MM-DD")}).attr("x",function(C){return f(C.start)+v}).attr("y",a.gridLineStartPadding).attr("width",function(C){const E=C.end.add(1,"day");return f(E)-f(C.start)}).attr("height",s-b-a.gridLineStartPadding).attr("transform-origin",function(C,E){return(f(C.start)+v+.5*(f(C.end)-f(C.start))).toString()+"px "+(E*k+.5*s).toString()+"px"}).attr("class","exclude-range")}function G(k,b,v,y){let s=ye(f).tickSize(-y+b+a.gridLineStartPadding).tickFormat(Wt(r.db.getAxisFormat()||a.axisFormat||"%Y-%m-%d"));const d=/^([1-9]\d*)(millisecond|second|minute|hour|day|week|month)$/.exec(r.db.getTickInterval()||a.tickInterval);if(d!==null){const o=d[1],g=d[2],e=r.db.getWeekday()||a.weekday;switch(g){case"millisecond":s.ticks(Nt.every(o));break;case"second":s.ticks(Rt.every(o));break;case"minute":s.ticks(zt.every(o));break;case"hour":s.ticks(Pt.every(o));break;case"day":s.ticks(Ot.every(o));break;case"week":s.ticks(Gt[e].every(o));break;case"month":s.ticks(Vt.every(o));break}}if(z.append("g").attr("class","grid").attr("transform","translate("+k+", "+(y-50)+")").call(s).selectAll("text").style("text-anchor","middle").attr("fill","#000").attr("stroke","none").attr("font-size",10).attr("dy","1em"),r.db.topAxisEnabled()||a.topAxis){let o=ge(f).tickSize(-y+b+a.gridLineStartPadding).tickFormat(Wt(r.db.getAxisFormat()||a.axisFormat||"%Y-%m-%d"));if(d!==null){const g=d[1],e=d[2],I=r.db.getWeekday()||a.weekday;switch(e){case"millisecond":o.ticks(Nt.every(g));break;case"second":o.ticks(Rt.every(g));break;case"minute":o.ticks(zt.every(g));break;case"hour":o.ticks(Pt.every(g));break;case"day":o.ticks(Ot.every(g));break;case"week":o.ticks(Gt[I].every(g));break;case"month":o.ticks(Vt.every(g));break}}z.append("g").attr("class","grid").attr("transform","translate("+k+", "+b+")").call(o).selectAll("text").style("text-anchor","middle").attr("fill","#000").attr("stroke","none").attr("font-size",10)}}function U(k,b){let v=0;const y=Object.keys(W).map(s=>[s,W[s]]);z.append("g").selectAll("text").data(y).enter().append(function(s){const u=s[0].split(pe.lineBreakRegex),d=-(u.length-1)/2,o=F.createElementNS("http://www.w3.org/2000/svg","text");o.setAttribute("dy",d+"em");for(const[g,e]of u.entries()){const I=F.createElementNS("http://www.w3.org/2000/svg","tspan");I.setAttribute("alignment-baseline","central"),I.setAttribute("x","10"),g>0&&I.setAttribute("dy","1em"),I.textContent=e,o.appendChild(I)}return o}).attr("x",10).attr("y",function(s,u){if(u>0)for(let d=0;d<u;d++)return v+=y[u-1][1],s[1]*k/2+v*k+b;else return s[1]*k/2+b}).attr("font-size",a.sectionFontSize).attr("class",function(s){for(const[u,d]of S.entries())if(s[0]===d)return"sectionTitle sectionTitle"+u%a.numberSectionStyles;return"sectionTitle"})}function X(k,b,v,y){const s=r.db.getTodayMarker();if(s==="off")return;const u=z.append("g").attr("class","today"),d=new Date,o=u.append("line");o.attr("x1",f(d)+k).attr("x2",f(d)+k).attr("y1",a.titleTopMargin).attr("y2",y-a.titleTopMargin).attr("class","today"),s!==""&&o.attr("style",s.replace(/,/g,";"))}function H(k){const b={},v=[];for(let y=0,s=k.length;y<s;++y)Object.prototype.hasOwnProperty.call(b,k[y])||(b[k[y]]=!0,v.push(k[y]));return v}},ps={setConf:ks,draw:gs},vs=t=>`
  .mermaid-main-font {
    font-family: var(--mermaid-font-family, "trebuchet ms", verdana, arial, sans-serif);
  }

  .exclude-range {
    fill: ${t.excludeBkgColor};
  }

  .section {
    stroke: none;
    opacity: 0.2;
  }

  .section0 {
    fill: ${t.sectionBkgColor};
  }

  .section2 {
    fill: ${t.sectionBkgColor2};
  }

  .section1,
  .section3 {
    fill: ${t.altSectionBkgColor};
    opacity: 0.2;
  }

  .sectionTitle0 {
    fill: ${t.titleColor};
  }

  .sectionTitle1 {
    fill: ${t.titleColor};
  }

  .sectionTitle2 {
    fill: ${t.titleColor};
  }

  .sectionTitle3 {
    fill: ${t.titleColor};
  }

  .sectionTitle {
    text-anchor: start;
    font-family: var(--mermaid-font-family, "trebuchet ms", verdana, arial, sans-serif);
  }


  /* Grid and axis */

  .grid .tick {
    stroke: ${t.gridColor};
    opacity: 0.8;
    shape-rendering: crispEdges;
  }

  .grid .tick text {
    font-family: ${t.fontFamily};
    fill: ${t.textColor};
  }

  .grid path {
    stroke-width: 0;
  }


  /* Today line */

  .today {
    fill: none;
    stroke: ${t.todayLineColor};
    stroke-width: 2px;
  }


  /* Task styling */

  /* Default task */

  .task {
    stroke-width: 2;
  }

  .taskText {
    text-anchor: middle;
    font-family: var(--mermaid-font-family, "trebuchet ms", verdana, arial, sans-serif);
  }

  .taskTextOutsideRight {
    fill: ${t.taskTextDarkColor};
    text-anchor: start;
    font-family: var(--mermaid-font-family, "trebuchet ms", verdana, arial, sans-serif);
  }

  .taskTextOutsideLeft {
    fill: ${t.taskTextDarkColor};
    text-anchor: end;
  }


  /* Special case clickable */

  .task.clickable {
    cursor: pointer;
  }

  .taskText.clickable {
    cursor: pointer;
    fill: ${t.taskTextClickableColor} !important;
    font-weight: bold;
  }

  .taskTextOutsideLeft.clickable {
    cursor: pointer;
    fill: ${t.taskTextClickableColor} !important;
    font-weight: bold;
  }

  .taskTextOutsideRight.clickable {
    cursor: pointer;
    fill: ${t.taskTextClickableColor} !important;
    font-weight: bold;
  }


  /* Specific task settings for the sections*/

  .taskText0,
  .taskText1,
  .taskText2,
  .taskText3 {
    fill: ${t.taskTextColor};
  }

  .task0,
  .task1,
  .task2,
  .task3 {
    fill: ${t.taskBkgColor};
    stroke: ${t.taskBorderColor};
  }

  .taskTextOutside0,
  .taskTextOutside2
  {
    fill: ${t.taskTextOutsideColor};
  }

  .taskTextOutside1,
  .taskTextOutside3 {
    fill: ${t.taskTextOutsideColor};
  }


  /* Active task */

  .active0,
  .active1,
  .active2,
  .active3 {
    fill: ${t.activeTaskBkgColor};
    stroke: ${t.activeTaskBorderColor};
  }

  .activeText0,
  .activeText1,
  .activeText2,
  .activeText3 {
    fill: ${t.taskTextDarkColor} !important;
  }


  /* Completed task */

  .done0,
  .done1,
  .done2,
  .done3 {
    stroke: ${t.doneTaskBorderColor};
    fill: ${t.doneTaskBkgColor};
    stroke-width: 2;
  }

  .doneText0,
  .doneText1,
  .doneText2,
  .doneText3 {
    fill: ${t.taskTextDarkColor} !important;
  }


  /* Tasks on the critical line */

  .crit0,
  .crit1,
  .crit2,
  .crit3 {
    stroke: ${t.critBorderColor};
    fill: ${t.critBkgColor};
    stroke-width: 2;
  }

  .activeCrit0,
  .activeCrit1,
  .activeCrit2,
  .activeCrit3 {
    stroke: ${t.critBorderColor};
    fill: ${t.activeTaskBkgColor};
    stroke-width: 2;
  }

  .doneCrit0,
  .doneCrit1,
  .doneCrit2,
  .doneCrit3 {
    stroke: ${t.critBorderColor};
    fill: ${t.doneTaskBkgColor};
    stroke-width: 2;
    cursor: pointer;
    shape-rendering: crispEdges;
  }

  .milestone {
    transform: rotate(45deg) scale(0.8,0.8);
  }

  .milestoneText {
    font-style: italic;
  }
  .doneCritText0,
  .doneCritText1,
  .doneCritText2,
  .doneCritText3 {
    fill: ${t.taskTextDarkColor} !important;
  }

  .activeCritText0,
  .activeCritText1,
  .activeCritText2,
  .activeCritText3 {
    fill: ${t.taskTextDarkColor} !important;
  }

  .titleText {
    text-anchor: middle;
    font-size: 18px;
    fill: ${t.titleColor||t.textColor};
    font-family: var(--mermaid-font-family, "trebuchet ms", verdana, arial, sans-serif);
  }
`,xs=vs,Ds={parser:Ye,db:ms,renderer:ps,styles:xs};export{Ds as diagram};
