import{_ as o}from"./index-1fd01b2f.js";import{U as l,ae as c,R as d,j as e,Y as s,Z as n,$ as m,H as u}from"./modulepreload-polyfill-ac150c65.js";import{P as p}from"./index-0ed67c98.js";const f=()=>{const t=l(),{mutate:i}=c(),a=d.useMemo(()=>[{colKey:"id",sorter:!0,sortType:"all",title:"ID",width:100},{colKey:"name",title:t("content.source.fields.name"),ellipsis:!0},{colKey:"created_at",title:t("content.source.fields.createdAt"),sorter:!0,sortType:"all",width:200},{colKey:"link",title:t("table.actions"),fixed:"right",align:"center",width:120,cell:({row:r})=>e.jsxs("div",{className:"flex justify-center gap-4",children:[e.jsx(s,{title:t("buttons.edit"),trigger:e.jsx(n,{theme:"primary",children:t("buttons.edit")}),component:()=>o(()=>import("./save-8bace11e.js"),["assets/save-8bace11e.js","assets/modulepreload-polyfill-ac150c65.js","assets/uploadImage-0a68a97f.js","assets/useUpload-c49e3844.js"]),componentProps:{id:r.id,menu_id:r.menu_id}}),e.jsx(p,{content:t("buttons.confirm"),destroyOnClose:!0,placement:"top",showArrow:!0,theme:"default",onConfirm:()=>{i({resource:"content.source",id:r.id})},children:e.jsx(n,{theme:"danger",children:t("buttons.delete")})})]})}],[t]);return e.jsx(m,{columns:a,actionRender:()=>e.jsx(e.Fragment,{children:e.jsx(s,{title:t("buttons.create"),trigger:e.jsx(u,{icon:e.jsx("div",{className:"i-tabler:plus mr-2"}),children:t("buttons.create")}),component:()=>o(()=>import("./save-8bace11e.js"),["assets/save-8bace11e.js","assets/modulepreload-polyfill-ac150c65.js","assets/uploadImage-0a68a97f.js","assets/useUpload-c49e3844.js"])})})})};export{f as default};