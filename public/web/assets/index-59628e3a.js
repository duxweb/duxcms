import{g as w,u as B,q as F,i as O,aj as G,aA as H,v as M,x as u,R as o,Q as X,w as A}from"./modulepreload-polyfill-ac2ad948.js";/**
 * tdesign v1.4.1
 * (c) 2023 tdesign
 * @license MIT
 */var Y={label:[],loading:!1,size:"medium"};/**
 * tdesign v1.4.1
 * (c) 2023 tdesign
 * @license MIT
 */var ee=["className","value","defaultValue","disabled","loading","size","label","customValue","onChange"];function k(e,r){var t=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);r&&(a=a.filter(function(d){return Object.getOwnPropertyDescriptor(e,d).enumerable})),t.push.apply(t,a)}return t}function x(e){for(var r=1;r<arguments.length;r++){var t=arguments[r]!=null?arguments[r]:{};r%2?k(Object(t),!0).forEach(function(a){u(e,a,t[a])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(t)):k(Object(t)).forEach(function(a){Object.defineProperty(e,a,Object.getOwnPropertyDescriptor(t,a))})}return e}var S=w.forwardRef(function(e,r){var t=B(),a=t.classPrefix,d=e.className,n=e.value,T=e.defaultValue,f=e.disabled,m=e.loading,z=e.size,v=e.label,s=e.customValue,g=e.onChange,D=F(e,ee),I=s||[],C=O(I,2),P=C[0],i=P===void 0?!0:P,_=C[1],R=_===void 0?!1:_,b=typeof n<"u",U=T===i||n===i,Z=w.useState(U),j=O(Z,2),c=j[0],N=j[1];function q(y){if(Array.isArray(v)){var l=O(v,2),E=l[0],Q=E===void 0?"":E,V=l[1],W=V===void 0?"":V,$=y?Q:W;return A($,{value:n})}return A(v,{value:n})}function J(y){if(!f){!b&&N(!c);var l=c?R:i;g==null||g(l,{e:y})}}w.useEffect(function(){Array.isArray(s)&&!s.includes(n)&&G.error("Switch","value is not in customValue: ".concat(JSON.stringify(s))),b&&N(n===i)},[n,s,i,b]);var p=H(),K=p.SIZE,h=p.STATUS,L=M("".concat(a,"-switch"),d,u(u(u({},h.checked,c),h.disabled,f),h.loading,m),K[z]);return o.createElement("button",x(x({},D),{},{type:"button",role:"switch",disabled:f||m,className:L,ref:r,onClick:J}),o.createElement("span",{className:"".concat(a,"-switch__handle")},m&&o.createElement(X,{loading:!0,size:"small"})),o.createElement("div",{className:"".concat(a,"-switch__content")},q(c)))});S.displayName="Switch";S.defaultProps=Y;/**
 * tdesign v1.4.1
 * (c) 2023 tdesign
 * @license MIT
 */var te=S;export{te as S};
