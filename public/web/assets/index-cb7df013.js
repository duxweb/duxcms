import{bM as _e,ax as ee,g as c,q as we,F as Oe,i as E,bN as Se,bO as te,bH as K,u as je,x as m,v as N,R as y,y as D,bP as Pe}from"./modulepreload-polyfill-ac2ad948.js";/**
 * tdesign v1.4.1
 * (c) 2023 tdesign
 * @license MIT
 */var Te=`
  min-height:0 !important;
  max-height:none !important;
  height:0 !important;
  visibility:hidden !important;
  overflow:hidden !important;
  position:absolute !important;
  z-index:-1000 !important;
  top:0 !important;
  right:0 !important
`,l;function ae(e){var o,i=arguments.length>1&&arguments[1]!==void 0?arguments[1]:1,n=arguments.length>2&&arguments[2]!==void 0?arguments[2]:null;l||(l=document.createElement("textarea"),document.body.appendChild(l));var r=_e(e),x=r.paddingSize,w=r.borderSize,O=r.boxSizing,z=r.sizingStyle;l.setAttribute("style","".concat(z,";").concat(Te)),l.value=e.value||e.placeholder||"";var f=l.scrollHeight,b={},_=O==="border-box",R=O==="content-box";_?f+=w:R&&(f-=x),l.value="";var S=l.scrollHeight-x;(o=l)===null||o===void 0||(o=o.parentNode)===null||o===void 0||o.removeChild(l),l=null;var j=function(P){var d=S*P;return _&&(d=d+x+w),d};if(!ee(i)){var s=j(i);f=Math.max(s,f),b.minHeight="".concat(s,"px")}return ee(n)||(f=Math.min(j(n),f)),b.height="".concat(f,"px"),b}/**
 * tdesign v1.4.1
 * (c) 2023 tdesign
 * @license MIT
 */var Ce={allowInputOverMax:!1,autofocus:!1,autosize:!1,placeholder:void 0,readonly:!1};/**
 * tdesign v1.4.1
 * (c) 2023 tdesign
 * @license MIT
 */var Ee=["disabled","maxlength","maxcharacter","className","readonly","autofocus","style","onKeydown","onKeypress","onKeyup","autosize","status","tips","allowInputOverMax"];function ne(e,o){var i=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);o&&(n=n.filter(function(r){return Object.getOwnPropertyDescriptor(e,r).enumerable})),i.push.apply(i,n)}return i}function A(e){for(var o=1;o<arguments.length;o++){var i=arguments[o]!=null?arguments[o]:{};o%2?ne(Object(i),!0).forEach(function(n){m(e,n,i[n])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(i)):ne(Object(i)).forEach(function(n){Object.defineProperty(e,n,Object.getOwnPropertyDescriptor(i,n))})}return e}var F=c.forwardRef(function(e,o){var i=e.disabled,n=e.maxlength,r=e.maxcharacter,x=e.className,w=e.readonly,O=e.autofocus,z=e.style,f=e.onKeydown,b=f===void 0?D:f,_=e.onKeypress,R=_===void 0?D:_,S=e.onKeyup,j=S===void 0?D:S,s=e.autosize,h=e.status,P=e.tips,d=e.allowInputOverMax,$=we(e,Ee),re=Oe(e,"value",e.onChange),U=E(re,2),B=U[0],p=B===void 0?"":B,oe=U[1],ie=c.useState(!1),V=E(ie,2),se=V[0],Z=V[1],ce=c.useState(!1),q=E(ce,2),ue=q[0],le=q[1],fe=c.useState({}),W=E(fe,2),de=W[0],H=W[1],T=c.useRef(!1),X=typeof r<"u",g=c.useRef(),Y=c.useRef(),M=c.useMemo(function(){return Se(p)},[p]),I=c.useMemo(function(){var t=te(String(p),d?1/0:r);return K(t)==="object"?t.length:t},[p,d,r]),ve=je(),u=ve.classPrefix,me=Object.keys($).filter(function(t){return!/^on[A-Z]/.test(t)}),ge=me.reduce(function(t,a){return Object.assign(t,m({},a,e[a]))},{}),xe=Object.keys($).filter(function(t){return/^on[A-Z]/.test(t)}),he=xe.reduce(function(t,a){return Object.assign(t,m({},a,function(v){i||(a==="onFocus"&&Z(!0),a==="onBlur"&&Z(!1),e[a](v.currentTarget.value,{e:v}))})),t},{}),pe=N("".concat(u,"-textarea__inner"),x,m(m(m(m({},"".concat(u,"-is-").concat(h),h),"".concat(u,"-is-disabled"),i),"".concat(u,"-is-focused"),se),"".concat(u,"-resize-none"),K(s)==="object")),C=c.useCallback(function(){s===!0?H(ae(g.current)):K(s)==="object"&&H(ae(g.current,s==null?void 0:s.minRows,s==null?void 0:s.maxRows))},[s]);c.useEffect(function(){C()},[g==null?void 0:g.current]);function k(t){var a=t.target,v=a.value;if(!d&&!T.current&&(v=Pe(v,n),r&&r>=0)){var Q=te(v,r);v=K(Q)==="object"&&Q.characters}oe(v,{e:t})}function ye(){T.current=!0}function be(t){T.current&&(T.current=!1,k(t))}var G=function(a,v){return y.createElement("span",{className:"".concat(u,"-textarea__limit")},ue&&d?y.createElement("span",{className:"".concat(u,"-textarea__tips--warning")}," ",a):"".concat(a),"/".concat(v))};c.useEffect(function(){s===!1&&H({height:"auto",minHeight:"auto"})},[C,s]),c.useEffect(function(){C()},[C,p]),c.useEffect(function(){d&&le(!!(n&&M>n)||!!(r&&I>r))},[d,I,M,r,n]),c.useImperativeHandle(o,function(){return{currentElement:Y.current,textareaElement:g.current}});var L=P&&y.createElement("div",{className:N("".concat(u,"-textarea__tips"),m(m({},"".concat(u,"-textarea__tips--normal"),!h),"".concat(u,"-textarea__tips--").concat(h),h))},P),J=X&&G(I,r)||!X&&n&&G(M,n);return y.createElement("div",{style:z,ref:Y,className:N("".concat(u,"-textarea"),x)},y.createElement("textarea",A(A(A({},ge),he),{},{value:p,style:de,className:pe,readOnly:w,autoFocus:O,disabled:i,onChange:k,onKeyDown:function(a){return b(a.currentTarget.value,{e:a})},onKeyPress:function(a){return R(a.currentTarget.value,{e:a})},onKeyUp:function(a){return j(a.currentTarget.value,{e:a})},onCompositionStart:ye,onCompositionEnd:be,ref:g})),L||J?y.createElement("div",{className:N("".concat(u,"-textarea__info_wrapper"),m({},"".concat(u,"-textarea__info_wrapper_align"),!L))},L,J):null)});F.displayName="Textarea";F.defaultProps=Ce;/**
 * tdesign v1.4.1
 * (c) 2023 tdesign
 * @license MIT
 */var Ne=F;export{Ne as T};
