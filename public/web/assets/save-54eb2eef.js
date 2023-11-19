import{cc as n,U as r,a4 as i,j as e,bJ as o,O as l,P as c}from"./modulepreload-polyfill-bcbe5797.js";/**
 * tdesign v1.3.0
 * (c) 2023 tdesign
 * @license MIT
 */var d=n;const j=s=>{const t=r(),{data:a}=i({method:"get",url:"system/role/permission"}),m=a==null?void 0:a.data;return e.jsxs(o,{id:s==null?void 0:s.id,children:[e.jsx(l.FormItem,{label:t("system.role.fields.name"),name:"name",children:e.jsx(c,{})}),e.jsx(l.FormItem,{label:t("system.role.fields.permission"),name:"permission",children:e.jsx(d,{keys:{value:"name",label:"label"},checkable:!0,data:m})})]})};export{j as default};
