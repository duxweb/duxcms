import{c8 as m,bf as U,bi as d,bj as f}from"./modulepreload-polyfill-bcbe5797.js";/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var x=m;const b=o=>{const{name:a,config:g}=U(),{data:p}=d();return{action:`${f()}/${a}/${g.apiPath.upload}`,headers:{Accept:"application/json",Authorization:(p==null?void 0:p.token)||""},formatResponse:t=>{var i,c,e,s,l,r;const u=t.XMLHttpRequest;if(u.status!=200){let n={};try{n=JSON.parse(u.response)}catch{}t.error=(n==null?void 0:n.message)||t.statusText}else t.url=(c=(i=t==null?void 0:t.data)==null?void 0:i[0])==null?void 0:c.url,t.name=(s=(e=t==null?void 0:t.data)==null?void 0:e[0])==null?void 0:s.name,t.size=(r=(l=t==null?void 0:t.data)==null?void 0:l[0])==null?void 0:r.size;return t},...o}},z=o=>o?typeof o=="string"?[{url:o}]:o:[],S=o=>{var a;return(a=o==null?void 0:o[0])==null?void 0:a.url};export{x as U,z as f,S as g,b as u};
