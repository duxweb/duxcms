import{bx as Pe,av as re,g as m,q as Ce,G as Te,i as K,by as Ee,bz as se,as as R,u as ze,x as p,v as F,R as S,y as D,bA as Ke,j as l,bB as Re,bC as oe,P as N,Q as ue,Y as Fe,ab as Ie,J as He}from"./modulepreload-polyfill-1cc1042d.js";/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var Me=`
  min-height:0 !important;
  max-height:none !important;
  height:0 !important;
  visibility:hidden !important;
  overflow:hidden !important;
  position:absolute !important;
  z-index:-1000 !important;
  top:0 !important;
  right:0 !important
`,h;function ce(e){var r,n=arguments.length>1&&arguments[1]!==void 0?arguments[1]:1,t=arguments.length>2&&arguments[2]!==void 0?arguments[2]:null;h||(h=document.createElement("textarea"),document.body.appendChild(h));var s=Pe(e),u=s.paddingSize,i=s.borderSize,v=s.boxSizing,a=s.sizingStyle;h.setAttribute("style","".concat(a,";").concat(Me)),h.value=e.value||e.placeholder||"";var d=h.scrollHeight,y={},_=v==="border-box",I=v==="content-box";_?d+=i:I&&(d-=u),h.value="";var C=h.scrollHeight-u;(r=h)===null||r===void 0||(r=r.parentNode)===null||r===void 0||r.removeChild(h),h=null;var T=function(f){var b=C*f;return _&&(b=b+u+i),b};if(!re(n)){var P=T(n);d=Math.max(P,d),y.minHeight="".concat(P,"px")}return re(t)||(d=Math.min(T(t),d)),y.height="".concat(d,"px"),y}/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var Le={allowInputOverMax:!1,autofocus:!1,autosize:!1,placeholder:void 0,readonly:!1};/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var Ae=["disabled","maxlength","maxcharacter","className","readonly","autofocus","style","onKeydown","onKeypress","onKeyup","autosize","status","tips","allowInputOverMax"];function ie(e,r){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var t=Object.getOwnPropertySymbols(e);r&&(t=t.filter(function(s){return Object.getOwnPropertyDescriptor(e,s).enumerable})),n.push.apply(n,t)}return n}function $(e){for(var r=1;r<arguments.length;r++){var n=arguments[r]!=null?arguments[r]:{};r%2?ie(Object(n),!0).forEach(function(t){p(e,t,n[t])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):ie(Object(n)).forEach(function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))})}return e}var B=m.forwardRef(function(e,r){var n,t,s=e.disabled,u=e.maxlength,i=e.maxcharacter,v=e.className,a=e.readonly,d=e.autofocus,y=e.style,_=e.onKeydown,I=_===void 0?D:_,C=e.onKeypress,T=C===void 0?D:C,P=e.onKeyup,U=P===void 0?D:P,f=e.autosize,b=e.status,V=e.tips,O=e.allowInputOverMax,q=Ce(e,Ae),le=Te(e,"value",e.onChange),Y=K(le,2),Z=Y[0],w=Z===void 0?"":Z,me=Y[1],de=m.useState(!1),k=K(de,2),fe=k[0],G=k[1],xe=m.useState(!1),J=K(xe,2),ve=J[0],he=J[1],ge=m.useState({}),Q=K(ge,2),pe=Q[0],H=Q[1],E=m.useRef(!1),W=typeof i<"u",j=m.useRef(),X=m.useRef(),M=m.useMemo(function(){return Ee(w)},[w]),L=m.useMemo(function(){var o=se(String(w),O?1/0:i);return R(o)==="object"?o.length:o},[w,O,i]),be=ze(),x=be.classPrefix,ye=Object.keys(q).filter(function(o){return!/^on[A-Z]/.test(o)}),_e=ye.reduce(function(o,c){return Object.assign(o,p({},c,e[c]))},{}),je=Object.keys(q).filter(function(o){return/^on[A-Z]/.test(o)}),Oe=je.reduce(function(o,c){return Object.assign(o,p({},c,function(g){s||(c==="onFocus"&&G(!0),c==="onBlur"&&G(!1),e[c](g.currentTarget.value,{e:g}))})),o},{}),we=F("".concat(x,"-textarea__inner"),v,(n={},p(n,"".concat(x,"-is-").concat(b),b),p(n,"".concat(x,"-is-disabled"),s),p(n,"".concat(x,"-is-focused"),fe),p(n,"".concat(x,"-resize-none"),R(f)==="object"),n)),z=m.useCallback(function(){f===!0?H(ce(j.current)):R(f)==="object"&&H(ce(j.current,f==null?void 0:f.minRows,f==null?void 0:f.maxRows))},[f]);m.useEffect(function(){z()},[j==null?void 0:j.current]);function ee(o){var c=o.target,g=c.value;if(!O&&!E.current&&(g=Ke(g,u),i&&i>=0)){var ne=se(g,i);g=R(ne)==="object"&&ne.characters}me(g,{e:o})}function Se(){E.current=!0}function Ne(o){E.current&&(E.current=!1,ee(o))}var te=function(c,g){return S.createElement("span",{className:"".concat(x,"-textarea__limit")},ve&&O?S.createElement("span",{className:"".concat(x,"-textarea__tips--warning")}," ",c):"".concat(c),"/".concat(g))};m.useEffect(function(){f===!1&&H({height:"auto",minHeight:"auto"})},[z,f]),m.useEffect(function(){z()},[z,w]),m.useEffect(function(){O&&he(!!(u&&M>u)||!!(i&&L>i))},[O,L,M,i,u]),m.useImperativeHandle(r,function(){return{currentElement:X.current,textareaElement:j.current}});var A=V&&S.createElement("div",{className:F("".concat(x,"-textarea__tips"),(t={},p(t,"".concat(x,"-textarea__tips--normal"),!b),p(t,"".concat(x,"-textarea__tips--").concat(b),b),t))},V),ae=W&&te(L,i)||!W&&u&&te(M,u);return S.createElement("div",{style:y,ref:X,className:F("".concat(x,"-textarea"),v)},S.createElement("textarea",$($($({},_e),Oe),{},{value:w,style:pe,className:we,readOnly:a,autoFocus:d,disabled:s,onChange:ee,onKeyDown:function(c){return I(c.currentTarget.value,{e:c})},onKeyPress:function(c){return T(c.currentTarget.value,{e:c})},onKeyUp:function(c){return U(c.currentTarget.value,{e:c})},onCompositionStart:Se,onCompositionEnd:Ne,ref:j})),A||ae?S.createElement("div",{className:F("".concat(x,"-textarea__info_wrapper"),p({},"".concat(x,"-textarea__info_wrapper_align"),!A))},A,ae):null)});B.displayName="Textarea";B.defaultProps=Le;/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var De=B;const Ue=e=>{const[r,n]=m.useState({});return l.jsx(Re,{id:e==null?void 0:e.id,onRef:t=>{var s,u,i;n((i=(u=(s=t.current)==null?void 0:s.result)==null?void 0:u.queryResult)==null?void 0:i.data)},padding:!1,children:l.jsx(oe,{placement:"top",size:"medium",defaultValue:0,children:Object.entries((r==null?void 0:r.meta)||{}).filter(([t])=>t!="theme").map(([t,s],u)=>{const i=Object.entries((s==null?void 0:s.fields)||{});return l.jsx(oe.TabPanel,{value:u,label:s==null?void 0:s.name,destroyOnHide:!1,children:l.jsx("div",{className:"p-5",children:i.map(([v,a],d)=>{if((a==null?void 0:a.type)=="textarea")return l.jsx(N.FormItem,{name:[t,v],label:a==null?void 0:a.label,children:l.jsx(De,{})},d);if((a==null?void 0:a.type)=="text")return l.jsx(N.FormItem,{name:[t,v],label:a==null?void 0:a.label,children:l.jsx(ue,{})},d);if((a==null?void 0:a.type)=="list")return l.jsx($e,{name:[t,v],items:a==null?void 0:a.fields},d)})})},u)})})})},$e=({name:e,items:r})=>{const n=Fe();return l.jsx(N.FormList,{name:e,children:(t,{add:s,remove:u})=>l.jsxs(l.Fragment,{children:[t.map(({key:i,name:v,...a})=>l.jsxs("div",{className:"flex gap-4",children:[Object.entries(r||{}).map(([d,y],_)=>m.createElement(N.FormItem,{...a,key:_,name:[v,d],label:y,className:"w-0 flex-1"},l.jsx(ue,{className:"w-full"}))),l.jsx(N.FormItem,{label:" ",className:"flex-none",children:l.jsx(Ie,{size:"20px",name:"delete",style:{cursor:"pointer"},onClick:()=>u(v)})})]},i)),l.jsx(N.FormItem,{children:l.jsx(He,{theme:"default",variant:"dashed",onClick:()=>s({}),children:n("cms.theme.addField")})})]})})};export{Ue as default};