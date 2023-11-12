import{bJ as Oe,ax as ae,g as i,q as Se,F as je,i as P,bK as Te,bL as ne,av as C,u as Ke,x as m,v as E,R as b,y as D,bM as Ne}from"./modulepreload-polyfill-ac150c65.js";/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var Pe=`
  min-height:0 !important;
  max-height:none !important;
  height:0 !important;
  visibility:hidden !important;
  overflow:hidden !important;
  position:absolute !important;
  z-index:-1000 !important;
  top:0 !important;
  right:0 !important
`,l;function re(e){var o,n=arguments.length>1&&arguments[1]!==void 0?arguments[1]:1,r=arguments.length>2&&arguments[2]!==void 0?arguments[2]:null;l||(l=document.createElement("textarea"),document.body.appendChild(l));var f=Oe(e),d=f.paddingSize,c=f.borderSize,_=f.boxSizing,z=f.sizingStyle;l.setAttribute("style","".concat(z,";").concat(Pe)),l.value=e.value||e.placeholder||"";var g=l.scrollHeight,w={},O=_==="border-box",R=_==="content-box";O?g+=c:R&&(g-=d),l.value="";var j=l.scrollHeight-d;(o=l)===null||o===void 0||(o=o.parentNode)===null||o===void 0||o.removeChild(l),l=null;var T=function(s){var x=j*s;return O&&(x=x+d+c),x};if(!ae(n)){var S=T(n);g=Math.max(S,g),w.minHeight="".concat(S,"px")}return ae(r)||(g=Math.min(T(r),g)),w.height="".concat(g,"px"),w}/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var Ce={allowInputOverMax:!1,autofocus:!1,autosize:!1,placeholder:void 0,readonly:!1};/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var Ee=["disabled","maxlength","maxcharacter","className","readonly","autofocus","style","onKeydown","onKeypress","onKeyup","autosize","status","tips","allowInputOverMax"];function oe(e,o){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);o&&(r=r.filter(function(f){return Object.getOwnPropertyDescriptor(e,f).enumerable})),n.push.apply(n,r)}return n}function A(e){for(var o=1;o<arguments.length;o++){var n=arguments[o]!=null?arguments[o]:{};o%2?oe(Object(n),!0).forEach(function(r){m(e,r,n[r])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):oe(Object(n)).forEach(function(r){Object.defineProperty(e,r,Object.getOwnPropertyDescriptor(n,r))})}return e}var F=i.forwardRef(function(e,o){var n,r,f=e.disabled,d=e.maxlength,c=e.maxcharacter,_=e.className,z=e.readonly,g=e.autofocus,w=e.style,O=e.onKeydown,R=O===void 0?D:O,j=e.onKeypress,T=j===void 0?D:j,S=e.onKeyup,$=S===void 0?D:S,s=e.autosize,x=e.status,U=e.tips,p=e.allowInputOverMax,B=Se(e,Ee),ie=je(e,"value",e.onChange),V=P(ie,2),Z=V[0],y=Z===void 0?"":Z,se=V[1],ce=i.useState(!1),q=P(ce,2),ue=q[0],J=q[1],le=i.useState(!1),W=P(le,2),fe=W[0],de=W[1],ve=i.useState({}),X=P(ve,2),me=X[0],H=X[1],K=i.useRef(!1),Y=typeof c<"u",h=i.useRef(),k=i.useRef(),M=i.useMemo(function(){return Te(y)},[y]),L=i.useMemo(function(){var t=ne(String(y),p?1/0:c);return C(t)==="object"?t.length:t},[y,p,c]),ge=Ke(),u=ge.classPrefix,xe=Object.keys(B).filter(function(t){return!/^on[A-Z]/.test(t)}),he=xe.reduce(function(t,a){return Object.assign(t,m({},a,e[a]))},{}),pe=Object.keys(B).filter(function(t){return/^on[A-Z]/.test(t)}),ye=pe.reduce(function(t,a){return Object.assign(t,m({},a,function(v){f||(a==="onFocus"&&J(!0),a==="onBlur"&&J(!1),e[a](v.currentTarget.value,{e:v}))})),t},{}),be=E("".concat(u,"-textarea__inner"),_,(n={},m(n,"".concat(u,"-is-").concat(x),x),m(n,"".concat(u,"-is-disabled"),f),m(n,"".concat(u,"-is-focused"),ue),m(n,"".concat(u,"-resize-none"),C(s)==="object"),n)),N=i.useCallback(function(){s===!0?H(re(h.current)):C(s)==="object"&&H(re(h.current,s==null?void 0:s.minRows,s==null?void 0:s.maxRows))},[s]);i.useEffect(function(){N()},[h==null?void 0:h.current]);function G(t){var a=t.target,v=a.value;if(!p&&!K.current&&(v=Ne(v,d),c&&c>=0)){var te=ne(v,c);v=C(te)==="object"&&te.characters}se(v,{e:t})}function _e(){K.current=!0}function we(t){K.current&&(K.current=!1,G(t))}var Q=function(a,v){return b.createElement("span",{className:"".concat(u,"-textarea__limit")},fe&&p?b.createElement("span",{className:"".concat(u,"-textarea__tips--warning")}," ",a):"".concat(a),"/".concat(v))};i.useEffect(function(){s===!1&&H({height:"auto",minHeight:"auto"})},[N,s]),i.useEffect(function(){N()},[N,y]),i.useEffect(function(){p&&de(!!(d&&M>d)||!!(c&&L>c))},[p,L,M,c,d]),i.useImperativeHandle(o,function(){return{currentElement:k.current,textareaElement:h.current}});var I=U&&b.createElement("div",{className:E("".concat(u,"-textarea__tips"),(r={},m(r,"".concat(u,"-textarea__tips--normal"),!x),m(r,"".concat(u,"-textarea__tips--").concat(x),x),r))},U),ee=Y&&Q(L,c)||!Y&&d&&Q(M,d);return b.createElement("div",{style:w,ref:k,className:E("".concat(u,"-textarea"),_)},b.createElement("textarea",A(A(A({},he),ye),{},{value:y,style:me,className:be,readOnly:z,autoFocus:g,disabled:f,onChange:G,onKeyDown:function(a){return R(a.currentTarget.value,{e:a})},onKeyPress:function(a){return T(a.currentTarget.value,{e:a})},onKeyUp:function(a){return $(a.currentTarget.value,{e:a})},onCompositionStart:_e,onCompositionEnd:we,ref:h})),I||ee?b.createElement("div",{className:E("".concat(u,"-textarea__info_wrapper"),m({},"".concat(u,"-textarea__info_wrapper_align"),!I))},I,ee):null)});F.displayName="Textarea";F.defaultProps=Ce;/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var Re=F;export{Re as T};
