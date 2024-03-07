function __vite__mapDeps(indexes) {
  if (!__vite__mapDeps.viteFileDeps) {
    __vite__mapDeps.viteFileDeps = ["assets/save-DTcwxLRi.js","assets/modulepreload-polyfill-DkiKXnSB.js"]
  }
  return indexes.map((i) => __vite__mapDeps.viteFileDeps[i])
}
import{_ as i}from"./index-CInEZ-VQ.js";import{X as l,s,j as t,a7 as a}from"./modulepreload-polyfill-DkiKXnSB.js";import{b as n}from"./button-D0ULnvDG.js";import{a as c,D as d}from"./link-JNlAaly3.js";import"./action-BYcpOc6q.js";import"./index-BEGpCFfB.js";const j=()=>{const e=l(),r=s.useMemo(()=>[{colKey:"id",sorter:!0,sortType:"all",title:"ID",width:150},{colKey:"from",title:e("content.replace.fields.from"),ellipsis:!0},{colKey:"to",title:e("content.replace.fields.to"),ellipsis:!0},{colKey:"link",title:e("table.actions"),fixed:"right",align:"center",width:160,cell:({row:o})=>t.jsxs("div",{className:"flex justify-center gap-4",children:[t.jsx(c,{rowId:o.id,component:()=>i(()=>import("./save-DTcwxLRi.js"),__vite__mapDeps([0,1]))}),t.jsx(d,{rowId:o.id})]})}],[e]);return t.jsx(a,{columns:r,table:{rowKey:"id"},title:e("content.replace.name"),actionRender:()=>t.jsx(n,{component:()=>i(()=>import("./save-DTcwxLRi.js"),__vite__mapDeps([0,1]))})})};export{j as default};
