import{bW as we,bf as ee,g as o,p as Oe,q as Se,G as je,i as K,bX as Te,bY as te,bb as N,u as Pe,y as p,w as z,s as _,B as A,bZ as Ce}from"./modulepreload-polyfill-DkiKXnSB.js";/**
 * tdesign v1.5.2
 * (c) 2024 tdesign
 * @license MIT
 */var Ee=`
  min-height:0 !important;
  max-height:none !important;
  height:0 !important;
  visibility:hidden !important;
  overflow:hidden !important;
  position:absolute !important;
  z-index:-1000 !important;
  top:0 !important;
  right:0 !important
`,f;function ae(s){var r,e=arguments.length>1&&arguments[1]!==void 0?arguments[1]:1,n=arguments.length>2&&arguments[2]!==void 0?arguments[2]:null;f||(f=document.createElement("textarea"),document.body.appendChild(f));var c=we(s),i=c.paddingSize,w=c.borderSize,S=c.boxSizing,R=c.sizingStyle;f.setAttribute("style","".concat(R,";").concat(Ee)),f.value=s.value||s.placeholder||"";var v=f.scrollHeight,h={},j=S==="border-box",T=S==="content-box";j?v+=w:T&&(v-=i),f.value="";var H=f.scrollHeight-i;(r=f)===null||r===void 0||(r=r.parentNode)===null||r===void 0||r.removeChild(f),f=null;var O=function(m){var x=H*m;return j&&(x=x+i+w),x};if(!ee(e)){var P=O(e);v=Math.max(P,v),h.minHeight="".concat(P,"px")}return ee(n)||(v=Math.min(O(n),v)),h.height="".concat(v,"px"),h}/**
 * tdesign v1.5.2
 * (c) 2024 tdesign
 * @license MIT
 */var Ke={allowInputOverMax:!1,autofocus:!1,autosize:!1,placeholder:void 0,readonly:!1};/**
 * tdesign v1.5.2
 * (c) 2024 tdesign
 * @license MIT
 */var Ne=["disabled","maxlength","maxcharacter","className","readonly","autofocus","style","onKeydown","onKeypress","onKeyup","autosize","status","tips","allowInputOverMax"];function re(s,r){var e=Object.keys(s);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(s);r&&(n=n.filter(function(c){return Object.getOwnPropertyDescriptor(s,c).enumerable})),e.push.apply(e,n)}return e}function $(s){for(var r=1;r<arguments.length;r++){var e=arguments[r]!=null?arguments[r]:{};r%2?re(Object(e),!0).forEach(function(n){p(s,n,e[n])}):Object.getOwnPropertyDescriptors?Object.defineProperties(s,Object.getOwnPropertyDescriptors(e)):re(Object(e)).forEach(function(n){Object.defineProperty(s,n,Object.getOwnPropertyDescriptor(e,n))})}return s}var ne=o.forwardRef(function(s,r){var e=Oe(s,Ke),n=e.disabled,c=e.maxlength,i=e.maxcharacter,w=e.className,S=e.readonly,R=e.autofocus,v=e.style,h=e.onKeydown,j=h===void 0?A:h,T=e.onKeypress,H=T===void 0?A:T,O=e.onKeyup,P=O===void 0?A:O,u=e.autosize,m=e.status,x=e.tips,y=e.allowInputOverMax,F=Se(e,Ne),oe=je(e,"value",e.onChange),U=K(oe,2),B=U[0],b=B===void 0?"":B,se=U[1],ie=o.useState(!1),Z=K(ie,2),ce=Z[0],V=Z[1],ue=o.useState(!1),W=K(ue,2),le=W[0],fe=W[1],de=o.useState({}),X=K(de,2),pe=X[0],M=X[1],C=o.useRef(!1),Y=typeof i<"u",g=o.useRef(),q=o.useRef(),D=o.useMemo(function(){return Te(b)},[b]),I=o.useMemo(function(){var t=te(String(b),y?1/0:i);return N(t)==="object"?t.length:t},[b,y,i]),ve=Pe(),l=ve.classPrefix,me=Object.keys(F).filter(function(t){return!/^on[A-Z]/.test(t)}),ge=me.reduce(function(t,a){return Object.assign(t,p({},a,e[a]))},{}),he=Object.keys(F).filter(function(t){return/^on[A-Z]/.test(t)}),xe=he.reduce(function(t,a){return Object.assign(t,p({},a,function(d){n||(a==="onFocus"&&V(!0),a==="onBlur"&&V(!1),e[a](d.currentTarget.value,{e:d}))})),t},{}),ye=z("".concat(l,"-textarea__inner"),w,p(p(p(p({},"".concat(l,"-is-").concat(m),m),"".concat(l,"-is-disabled"),n),"".concat(l,"-is-focused"),ce),"".concat(l,"-resize-none"),N(u)==="object")),E=o.useCallback(function(){u===!0?M(ae(g.current)):N(u)==="object"&&M(ae(g.current,u==null?void 0:u.minRows,u==null?void 0:u.maxRows))},[u]);o.useEffect(function(){E()},[g==null?void 0:g.current]);function G(t){var a=t.target,d=a.value;if(!y&&!C.current&&(d=Ce(d,c),i&&i>=0)){var Q=te(d,i);d=N(Q)==="object"&&Q.characters}se(d,{e:t})}function be(){C.current=!0}function _e(t){C.current&&(C.current=!1,G(t))}var k=function(a,d){return _.createElement("span",{className:"".concat(l,"-textarea__limit")},le&&y?_.createElement("span",{className:"".concat(l,"-textarea__tips--warning")}," ",a):"".concat(a),"/".concat(d))};o.useEffect(function(){u===!1&&M({height:"auto",minHeight:"auto"})},[E,u]),o.useEffect(function(){E()},[E,b]),o.useEffect(function(){y&&fe(!!(c&&D>c)||!!(i&&I>i))},[y,I,D,i,c]),o.useImperativeHandle(r,function(){return{currentElement:q.current,textareaElement:g.current}});var L=x&&_.createElement("div",{className:z("".concat(l,"-textarea__tips"),p(p({},"".concat(l,"-textarea__tips--normal"),!m),"".concat(l,"-textarea__tips--").concat(m),m))},x),J=Y&&k(I,i)||!Y&&c&&k(D,c);return _.createElement("div",{style:v,ref:q,className:z("".concat(l,"-textarea"),w)},_.createElement("textarea",$($($({},ge),xe),{},{value:b,style:pe,className:ye,readOnly:S,autoFocus:R,disabled:n,onChange:G,onKeyDown:function(a){return j(a.currentTarget.value,{e:a})},onKeyPress:function(a){return H(a.currentTarget.value,{e:a})},onKeyUp:function(a){return P(a.currentTarget.value,{e:a})},onCompositionStart:be,onCompositionEnd:_e,ref:g})),L||J?_.createElement("div",{className:z("".concat(l,"-textarea__info_wrapper"),p({},"".concat(l,"-textarea__info_wrapper_align"),!L))},L,J):null)});ne.displayName="Textarea";/**
 * tdesign v1.5.2
 * (c) 2024 tdesign
 * @license MIT
 */var Re=ne;export{Re as T};
