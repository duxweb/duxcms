import{Y as d,Z as c,$ as f,j as t,bB as g,P as u}from"./modulepreload-polyfill-1cc1042d.js";import"./index-7256f177.js";import{M as x}from"./form-fa89c2c1.js";import{C as j}from"./index-ae91c890.js";import"./index-26810ad2.js";import"./index-9c2ee1aa.js";import"./uploadImage-82d541ff.js";const k=e=>{var r,i,m;const o=e.magic,l=d(),{data:a}=c({url:"tools/magic/config",method:"get",meta:{params:{magic:o}}}),{data:s,isLoading:n}=f({resource:"tools.data",meta:{params:{magic:o}},pagination:{mode:"off"}});return t.jsxs(g,{queryParams:{magic:o},id:e==null?void 0:e.id,children:[((r=a==null?void 0:a.data)==null?void 0:r.type)=="tree"&&t.jsx(u.FormItem,{label:l("tools.data.fields.parent"),name:"parent_id",children:t.jsx(j,{checkStrictly:!0,loading:n,options:s==null?void 0:s.data,keys:{label:"name",value:"id"},clearable:!0})}),((i=a==null?void 0:a.data)==null?void 0:i.fields)&&t.jsx(x,{fields:(m=a==null?void 0:a.data)==null?void 0:m.fields})]})};export{k as default};