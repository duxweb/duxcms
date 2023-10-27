import{g as w,u as G,q as H,i as O,bm as M,ay as Q,v as X,x as d,R as u,T as Y,w as k}from"./modulepreload-polyfill-1cc1042d.js";/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var ee={label:[],loading:!1,size:"medium"};/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var ae=["className","value","defaultValue","disabled","loading","size","label","customValue","onChange"];function x(e,r){var a=Object.keys(e);if(Object.getOwnPropertySymbols){var t=Object.getOwnPropertySymbols(e);r&&(t=t.filter(function(s){return Object.getOwnPropertyDescriptor(e,s).enumerable})),a.push.apply(a,t)}return a}function A(e){for(var r=1;r<arguments.length;r++){var a=arguments[r]!=null?arguments[r]:{};r%2?x(Object(a),!0).forEach(function(t){d(e,t,a[t])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(a)):x(Object(a)).forEach(function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(a,t))})}return e}var S=w.forwardRef(function(e,r){var a,t=G(),s=t.classPrefix,T=e.className,n=e.value,z=e.defaultValue,f=e.disabled,m=e.loading,D=e.size,v=e.label,i=e.customValue,g=e.onChange,I=H(e,ae),R=i||[],C=O(R,2),_=C[0],c=_===void 0?!0:_,P=C[1],U=P===void 0?!1:P,b=typeof n<"u",Z=z===c||n===c,q=w.useState(Z),N=O(q,2),l=N[0],j=N[1];function J(y){if(Array.isArray(v)){var o=O(v,2),E=o[0],$=E===void 0?"":E,V=o[1],B=V===void 0?"":V,F=y?$:B;return k(F,{value:n})}return k(v,{value:n})}function K(y){if(!f){!b&&j(!l);var o=l?U:c;g==null||g(o,{e:y})}}w.useEffect(function(){Array.isArray(i)&&!i.includes(n)&&M.error("Switch","value is not in customValue: ".concat(JSON.stringify(i))),b&&j(n===c)},[n,i,c,b]);var p=Q(),L=p.SIZE,h=p.STATUS,W=X("".concat(s,"-switch"),T,(a={},d(a,h.checked,l),d(a,h.disabled,f),d(a,h.loading,m),a),L[D]);return u.createElement("button",A(A({},I),{},{type:"button",role:"switch",disabled:f||m,className:W,ref:r,onClick:K}),u.createElement("span",{className:"".concat(s,"-switch__handle")},m&&u.createElement(Y,{loading:!0,size:"small"})),u.createElement("div",{className:"".concat(s,"-switch__content")},J(l)))});S.displayName="Switch";S.defaultProps=ee;/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var re=S;export{re as S};
