import{Y as d,$ as u,j as e,bB as c,P as n,Q as s}from"./modulepreload-polyfill-1cc1042d.js";import{U as x}from"./uploadImage-82d541ff.js";import{C as j}from"./index-ae91c890.js";const I=a=>{const t=d(),m=a.menu_id,{data:l,isLoading:i}=u({resource:"content.menuData",meta:{params:{menu_id:m}}}),r=(l==null?void 0:l.data)||[];return e.jsxs(c,{id:a==null?void 0:a.id,saveFormat:o=>({...o,menu_id:m}),queryParams:{menu_id:m},children:[e.jsx(n.FormItem,{label:t("content.menu.fields.parent"),name:"parent_id",children:e.jsx(j,{checkStrictly:!0,loading:i,options:r,keys:{label:"title",value:"id"},clearable:!0})}),e.jsx(n.FormItem,{label:t("content.menu.fields.title"),name:"title",children:e.jsx(s,{})}),e.jsx(n.FormItem,{label:t("content.menu.fields.subtitle"),name:"subtitle",children:e.jsx(s,{})}),e.jsx(n.FormItem,{label:t("content.menu.fields.url"),name:"url",children:e.jsx(s,{})}),e.jsx(n.FormItem,{label:t("content.menu.fields.image"),name:"image",children:e.jsx(x,{})})]})};export{I as default};