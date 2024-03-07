function __vite__mapDeps(indexes) {
  if (!__vite__mapDeps.viteFileDeps) {
    __vite__mapDeps.viteFileDeps = ["assets/save-2EZFUdfQ.js","assets/modulepreload-polyfill-DkiKXnSB.js"]
  }
  return indexes.map((i) => __vite__mapDeps.viteFileDeps[i])
}
import{_ as i}from"./index-CInEZ-VQ.js";import{X as r,s as a,j as t,a7 as l}from"./modulepreload-polyfill-DkiKXnSB.js";import{b as d}from"./button-D0ULnvDG.js";import{a as m,D as n}from"./link-JNlAaly3.js";import{S as p}from"./index-BL_4kOPz.js";import"./action-BYcpOc6q.js";import"./index-BEGpCFfB.js";const E=()=>{const e=r(),o=a.useMemo(()=>[{colKey:"id",sorter:!0,sortType:"all",title:"ID",width:150},{colKey:"secret_id",title:e("system.api.fields.secretId"),ellipsis:!0},{colKey:"secret_key",title:e("system.api.fields.secretKey"),ellipsis:!0},{colKey:"status",title:e("system.api.fields.status"),edit:{component:p,props:{},keepEditMode:!0}},{colKey:"link",title:e("table.actions"),fixed:"right",align:"center",width:160,cell:({row:s})=>t.jsxs("div",{className:"flex justify-center gap-4",children:[t.jsx(m,{rowId:s.id,component:()=>i(()=>import("./save-2EZFUdfQ.js"),__vite__mapDeps([0,1]))}),t.jsx(n,{rowId:s.id})]})}],[e]);return t.jsx(l,{columns:o,table:{rowKey:"id"},title:e("system.api.name"),actionRender:()=>t.jsx(d,{component:()=>i(()=>import("./save-2EZFUdfQ.js"),__vite__mapDeps([0,1]))})})};export{E as default};
