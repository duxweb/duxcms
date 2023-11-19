import{g as w,u as F,q as G,i as O,ai as H,az as M,v as X,x as d,R as u,Q as Y,w as k}from"./modulepreload-polyfill-bcbe5797.js";/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var ee={label:[],loading:!1,size:"medium"};/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var ae=["className","value","defaultValue","disabled","loading","size","label","customValue","onChange"];function x(e,r){var a=Object.keys(e);if(Object.getOwnPropertySymbols){var t=Object.getOwnPropertySymbols(e);r&&(t=t.filter(function(s){return Object.getOwnPropertyDescriptor(e,s).enumerable})),a.push.apply(a,t)}return a}function A(e){for(var r=1;r<arguments.length;r++){var a=arguments[r]!=null?arguments[r]:{};r%2?x(Object(a),!0).forEach(function(t){d(e,t,a[t])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(a)):x(Object(a)).forEach(function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(a,t))})}return e}var S=w.forwardRef(function(e,r){var a,t=F(),s=t.classPrefix,z=e.className,n=e.value,T=e.defaultValue,f=e.disabled,m=e.loading,D=e.size,v=e.label,i=e.customValue,g=e.onChange,I=G(e,ae),R=i||[],C=O(R,2),_=C[0],c=_===void 0?!0:_,P=C[1],U=P===void 0?!1:P,b=typeof n<"u",Z=T===c||n===c,q=w.useState(Z),N=O(q,2),l=N[0],j=N[1];function J(y){if(Array.isArray(v)){var o=O(v,2),E=o[0],W=E===void 0?"":E,V=o[1],$=V===void 0?"":V,B=y?W:$;return k(B,{value:n})}return k(v,{value:n})}function K(y){if(!f){!b&&j(!l);var o=l?U:c;g==null||g(o,{e:y})}}w.useEffect(function(){Array.isArray(i)&&!i.includes(n)&&H.error("Switch","value is not in customValue: ".concat(JSON.stringify(i))),b&&j(n===c)},[n,i,c,b]);var p=M(),L=p.SIZE,h=p.STATUS,Q=X("".concat(s,"-switch"),z,(a={},d(a,h.checked,l),d(a,h.disabled,f),d(a,h.loading,m),a),L[D]);return u.createElement("button",A(A({},I),{},{type:"button",role:"switch",disabled:f||m,className:Q,ref:r,onClick:K}),u.createElement("span",{className:"".concat(s,"-switch__handle")},m&&u.createElement(Y,{loading:!0,size:"small"})),u.createElement("div",{className:"".concat(s,"-switch__content")},J(l)))});S.displayName="Switch";S.defaultProps=ee;/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var re=S;export{re as S};
