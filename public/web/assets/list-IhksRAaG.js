function __vite__mapDeps(indexes) {
  if (!__vite__mapDeps.viteFileDeps) {
    __vite__mapDeps.viteFileDeps = ["assets/show-C8hDfj-d.js","assets/vendor-map-Dmo-f729.js","assets/vendor-react-CEAiij2-.js","assets/vendor-tdesign-Bh4nsAtR.js","assets/vendor-refine-DSrLypaZ.js","assets/modulepreload-polyfill-BJI5hq6_.js","assets/vendor-echarts-BA2V57Gb.js","assets/vendor-tinymce-DBANSpiv.js","assets/vendor-markdown-B5GQD1Nb.js","assets/vendor-lib-BfwGYCz4.js"]
  }
  return indexes.map((i) => __vite__mapDeps.viteFileDeps[i])
}
import{_ as d}from"./vendor-markdown-B5GQD1Nb.js";import{j as e}from"./vendor-map-Dmo-f729.js";import{A as p}from"./vendor-react-CEAiij2-.js";import{_ as h}from"./vendor-refine-DSrLypaZ.js";import{L as r,P as u,F as l}from"./modulepreload-polyfill-BJI5hq6_.js";import"./vendor-echarts-BA2V57Gb.js";import{a6 as x,x as j,Y as a,S as i,a4 as f}from"./vendor-tdesign-Bh4nsAtR.js";import{S as y}from"./link-Cm9EHL75.js";import"./vendor-tinymce-DBANSpiv.js";import"./vendor-lib-BfwGYCz4.js";import{u as v}from"./useSelect-Pcx3agwx.js";import"./action-DttMYyVn.js";const R=()=>{const s=h(),n=p.useMemo(()=>[{colKey:"id",sorter:!0,sortType:"all",title:"ID",width:100},{colKey:"username",title:s("system.operate.fields.user"),cell:({row:t})=>e.jsxs(r,{size:"small",children:[e.jsx(r.Avatar,{image:t.avatar,children:t.nickname[0]}),e.jsx(r.Title,{children:t.nickname}),e.jsx(r.Desc,{children:t.username})]})},{colKey:"request_method",title:s("system.operate.fields.request"),minWidth:210,cell:({row:t})=>e.jsxs("div",{className:"flex flex-col gap-2",children:[e.jsx("div",{children:e.jsx(x,{content:t.request_url,children:e.jsx(j,{children:t.route_name})})}),e.jsxs("div",{className:"flex gap-2",children:[e.jsx(a,{theme:"primary",variant:"outline",children:t.request_method}),e.jsx(a,{theme:"success",variant:"outline",children:t.request_time})]})]})},{colKey:"client_ip",title:s("system.operate.fields.client"),minWidth:200,cell:({row:t})=>e.jsxs("div",{className:"flex flex-col gap-2",children:[e.jsx("div",{children:t.client_ip}),e.jsx("div",{className:"flex gap-2",children:e.jsx(a,{theme:"primary",variant:"outline",children:t.client_device})})]})},{colKey:"time",title:s("system.operate.fields.requestTime"),minWidth:200},{colKey:"link",title:s("table.actions"),fixed:"right",align:"center",width:160,cell:({row:t})=>e.jsx("div",{className:"flex justify-center gap-4",children:e.jsx(y,{component:()=>d(()=>import("./show-C8hDfj-d.js"),__vite__mapDeps([0,1,2,3,4,5,6,7,8,9])),rowId:t.id})})}],[s]),{options:o,onSearch:c,queryResult:m}=v({resource:"system.user",optionLabel:"nickname",optionValue:"id"});return e.jsx(u,{columns:n,table:{rowKey:"id"},title:s("system.operate.name"),filterRender:()=>e.jsxs(e.Fragment,{children:[e.jsx(l,{name:"user",children:e.jsx(i,{filterable:!0,loading:m.isLoading,onSearch:c,options:o,placeholder:s("system.operate.filters.userPlaceholder"),clearable:!0})}),e.jsx(l,{name:"method",children:e.jsxs(i,{placeholder:s("system.operate.filters.method.placeholder"),clearable:!0,children:[e.jsx(i.Option,{value:"post",children:s("system.operate.filters.method.post")}),e.jsx(i.Option,{value:"put",children:s("system.operate.filters.method.put")}),e.jsx(i.Option,{value:"patch",children:s("system.operate.filters.method.patch")}),e.jsx(i.Option,{value:"delete",children:s("system.operate.filters.method.delete")})]})}),e.jsx(l,{name:"date",children:e.jsx(f,{enableTimePicker:!0,clearable:!0})})]})})};export{R as default};