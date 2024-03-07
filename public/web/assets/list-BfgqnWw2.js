function __vite__mapDeps(indexes) {
  if (!__vite__mapDeps.viteFileDeps) {
    __vite__mapDeps.viteFileDeps = ["assets/save-Dmr9otqt.js","assets/modulepreload-polyfill-DkiKXnSB.js"]
  }
  return indexes.map((i) => __vite__mapDeps.viteFileDeps[i])
}
import{_ as i}from"./index-CInEZ-VQ.js";import{X as a,s,j as t,a7 as n}from"./modulepreload-polyfill-DkiKXnSB.js";import{b as l}from"./button-D0ULnvDG.js";import{a as m,D as d}from"./link-JNlAaly3.js";import"./action-BYcpOc6q.js";import"./index-BEGpCFfB.js";const j=()=>{const e=a(),r=s.useMemo(()=>[{colKey:"id",sorter:!0,sortType:"all",title:"ID",width:150},{colKey:"name",title:e("content.attr.fields.name"),ellipsis:!0},{colKey:"link",title:e("table.actions"),fixed:"right",align:"center",width:160,cell:({row:o})=>t.jsxs("div",{className:"flex justify-center gap-4",children:[t.jsx(m,{rowId:o.id,component:()=>i(()=>import("./save-Dmr9otqt.js"),__vite__mapDeps([0,1]))}),t.jsx(d,{rowId:o.id})]})}],[e]);return t.jsx(n,{columns:r,table:{rowKey:"id"},title:e("content.attr.name"),actionRender:()=>t.jsx(l,{component:()=>i(()=>import("./save-Dmr9otqt.js"),__vite__mapDeps([0,1]))})})};export{j as default};
