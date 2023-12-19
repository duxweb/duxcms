import{cd as n,U as r,a4 as i,j as e,bK as o,O as l,P as d}from"./modulepreload-polyfill-ac2ad948.js";/**
 * tdesign v1.4.1
 * (c) 2023 tdesign
 * @license MIT
 */var c=n;const b=s=>{const t=r(),{data:a}=i({method:"get",url:"system/role/permission"}),m=a==null?void 0:a.data;return e.jsxs(o,{id:s==null?void 0:s.id,children:[e.jsx(l.FormItem,{label:t("system.role.fields.name"),name:"name",children:e.jsx(d,{})}),e.jsx(l.FormItem,{label:t("system.role.fields.permission"),name:"permission",children:e.jsx(c,{keys:{value:"name",label:"label"},checkable:!0,data:m})})]})};export{b as default};
