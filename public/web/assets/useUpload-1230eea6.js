import{c9 as m,bc as U,bd as g,be as f}from"./modulepreload-polyfill-ac2ad948.js";/**
 * tdesign v1.4.1
 * (c) 2023 tdesign
 * @license MIT
 */var $=m;const b=o=>{const{name:a,config:d}=U(),{data:p}=g();return{action:`${f()}/${a}/${d.apiPath.upload}`,headers:{Accept:"application/json",Authorization:(p==null?void 0:p.token)||""},formatResponse:t=>{var c,e,i,s,l,r;const u=t.XMLHttpRequest;if(u.status!=200){let n={};try{n=JSON.parse(u.response)}catch{}t.error=(n==null?void 0:n.message)||t.statusText}else t.url=(e=(c=t==null?void 0:t.data)==null?void 0:c[0])==null?void 0:e.url,t.name=(s=(i=t==null?void 0:t.data)==null?void 0:i[0])==null?void 0:s.name,t.size=(r=(l=t==null?void 0:t.data)==null?void 0:l[0])==null?void 0:r.size;return t},...o}},z=o=>o?typeof o=="string"?[{url:o}]:o:[],S=o=>{var a;return(a=o==null?void 0:o[0])==null?void 0:a.url};export{$ as U,z as f,S as g,b as u};
