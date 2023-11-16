import{c8 as g,bg as m,bj as U,bk as d}from"./modulepreload-polyfill-39569a5f.js";/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var h=g;const x=()=>{const{name:o,config:a}=m(),{data:p}=U();return{action:`${d()}/${o}/${a.apiPath.upload}`,headers:{Accept:"application/json",Authorization:(p==null?void 0:p.token)||""},formatResponse:t=>{var c,i,e,s,l,r;const u=t.XMLHttpRequest;if(u.status!=200){let n={};try{n=JSON.parse(u.response)}catch{}t.error=(n==null?void 0:n.message)||t.statusText}else t.url=(i=(c=t==null?void 0:t.data)==null?void 0:c[0])==null?void 0:i.url,t.name=(s=(e=t==null?void 0:t.data)==null?void 0:e[0])==null?void 0:s.name,t.size=(r=(l=t==null?void 0:t.data)==null?void 0:l[0])==null?void 0:r.size;return t}}},b=o=>o?typeof o=="string"?[{url:o}]:o:[],z=o=>{var a;return(a=o==null?void 0:o[0])==null?void 0:a.url};export{h as U,b as f,z as g,x as u};
