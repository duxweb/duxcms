import{s as o,u as B,p as F,q as G,i as w,g as E,x as V,ag as H,aS as Q,w as X,y as f,R as Y}from"./modulepreload-polyfill-DkiKXnSB.js";/**
 * tdesign v1.5.2
 * (c) 2024 tdesign
 * @license MIT
 */var ee={label:[],loading:!1,size:"medium"};/**
 * tdesign v1.5.2
 * (c) 2024 tdesign
 * @license MIT
 */var ae=["className","value","defaultValue","disabled","loading","size","label","customValue","onChange"];function k(r,s){var t=Object.keys(r);if(Object.getOwnPropertySymbols){var e=Object.getOwnPropertySymbols(r);s&&(e=e.filter(function(a){return Object.getOwnPropertyDescriptor(r,a).enumerable})),t.push.apply(t,e)}return t}function A(r){for(var s=1;s<arguments.length;s++){var t=arguments[s]!=null?arguments[s]:{};s%2?k(Object(t),!0).forEach(function(e){f(r,e,t[e])}):Object.getOwnPropertyDescriptors?Object.defineProperties(r,Object.getOwnPropertyDescriptors(t)):k(Object(t)).forEach(function(e){Object.defineProperty(r,e,Object.getOwnPropertyDescriptor(t,e))})}return r}var x=o.forwardRef(function(r,s){var t=B(),e=t.classPrefix,a=F(r,ee),D=a.className,n=a.value,T=a.defaultValue,v=a.disabled,m=a.loading,z=a.size,u=a.label,i=a.customValue,p=a.onChange,I=G(a,ae),R=i||[],S=w(R,2),O=S[0],c=O===void 0?!0:O,C=S[1],U=C===void 0?!1:C,g=typeof n<"u",Z=T===c||n===c,q=E.useState(Z),P=w(q,2),l=P[0],N=P[1],J=o.useMemo(function(){if(Array.isArray(u)){var h=w(u,2),d=h[0],y=d===void 0?"":d,j=h[1],W=j===void 0?"":j,$=l?y:W;return V($,{value:n})}return V(u,{value:n})},[u,l,n]),K=function(d){if(!v){!g&&N(!l);var y=l?U:c;p==null||p(y,{e:d})}};E.useEffect(function(){Array.isArray(i)&&!i.includes(n)&&H.error("Switch","value is not in customValue: ".concat(JSON.stringify(i))),g&&N(n===c)},[n,i,c,g]);var _=Q(),L=_.SIZE,b=_.STATUS,M=X("".concat(e,"-switch"),D,f(f(f({},b.checked,l),b.disabled,v),b.loading,m),L[z]);return o.createElement("button",A(A({},I),{},{type:"button",role:"switch",disabled:v||m,className:M,ref:s,onClick:K}),o.createElement("span",{className:"".concat(e,"-switch__handle")},m&&o.createElement(Y,{loading:!0,size:"small"})),o.createElement("div",{className:"".concat(e,"-switch__content")},J))});x.displayName="Switch";/**
 * tdesign v1.5.2
 * (c) 2024 tdesign
 * @license MIT
 */var re=x;export{re as S};
