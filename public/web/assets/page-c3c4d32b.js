import{ah as d,Y as u,ai as g,$ as j,j as e,aj as x,ak as h,P as s,Q as r,al as p,am as i,an as b}from"./modulepreload-polyfill-1cc1042d.js";import{F}from"./page-d7e14537.js";import{C as f}from"./index-ae91c890.js";const U=()=>{const n=d(),a=u(),{id:o}=g(),{data:l,isLoading:m}=j({resource:"content.category"}),c=(l==null?void 0:l.data)||[];return e.jsxs(F,{formProps:{labelAlign:"top"},back:!0,id:o,initFormat:t=>(t.image=x(t.image),t),saveFormat:t=>(t.image=h(t.image),t),settingRender:e.jsxs(e.Fragment,{children:[e.jsx(s.FormItem,{label:a("content.article.fields.category"),name:"class_id",children:e.jsx(f,{loading:m,options:c,keys:{label:"name",value:"id"},clearable:!0})}),e.jsx(s.FormItem,{label:a("content.article.fields.subtitle"),name:"subtitle",children:e.jsx(r,{})}),e.jsx(s.FormItem,{label:a("content.article.fields.image"),name:"image",children:e.jsx(p,{...n,theme:"image",accept:"image/*"})}),e.jsx(s.FormItem,{label:a("content.article.fields.author"),name:"author",children:e.jsx(r,{})}),e.jsx(s.FormItem,{label:a("content.article.fields.status"),name:"status",initialData:!0,children:e.jsxs(i.Group,{children:[e.jsx(i,{value:!0,children:a("content.article.tab.published")}),e.jsx(i,{value:!1,children:a("content.article.tab.unpublished")})]})})]}),children:[e.jsx(s.FormItem,{name:"title",children:e.jsx(r,{size:"large",placeholder:a("content.article.validate.title")})}),e.jsx(s.FormItem,{name:"content",children:e.jsx(b,{})})]})};export{U as default};