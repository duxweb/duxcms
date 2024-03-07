import{bU as m,aK as g,aL as d,aM as f}from"./modulepreload-polyfill-DkiKXnSB.js";/**
 * tdesign v1.5.2
 * (c) 2024 tdesign
 * @license MIT
 */var x=m;const z=a=>{const{name:o,config:U}=g(),{data:p}=d();return{action:`${f()}/${o}/${U.apiPath.upload}`,headers:{Accept:"application/json",Authorization:(p==null?void 0:p.token)||""},formatResponse:t=>{var i,c,e,l,r,s;const u=t.XMLHttpRequest;if(u.status!=200){let n={};try{n=JSON.parse(u.response)}catch{}t.error=(n==null?void 0:n.message)||t.statusText}else t.url=(c=(i=t==null?void 0:t.data)==null?void 0:i[0])==null?void 0:c.url,t.name=(l=(e=t==null?void 0:t.data)==null?void 0:e[0])==null?void 0:l.name,t.size=(s=(r=t==null?void 0:t.data)==null?void 0:r[0])==null?void 0:s.size;return t},...a}},M=a=>a?typeof a=="string"?[{url:a}]:a:[],S=a=>{var o;return(o=a==null?void 0:a[0])==null?void 0:o.url};export{x as U,M as f,S as g,z as u};
