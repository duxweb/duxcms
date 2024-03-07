function __vite__mapDeps(indexes) {
  if (!__vite__mapDeps.viteFileDeps) {
    __vite__mapDeps.viteFileDeps = ["assets/save-BgdlMpae.js","assets/modulepreload-polyfill-DkiKXnSB.js"]
  }
  return indexes.map((i) => __vite__mapDeps.viteFileDeps[i])
}
import{_ as i}from"./index-CInEZ-VQ.js";import{X as r,s as a,j as e,a7 as l}from"./modulepreload-polyfill-DkiKXnSB.js";import{b as n}from"./button-D0ULnvDG.js";import{a as m,D as d}from"./link-JNlAaly3.js";import"./action-BYcpOc6q.js";import"./index-BEGpCFfB.js";const j=()=>{const t=r(),s=a.useMemo(()=>[{colKey:"id",sorter:!0,sortType:"all",title:"ID",width:150},{colKey:"name",title:t("system.role.fields.name"),ellipsis:!0},{colKey:"link",title:t("table.actions"),fixed:"right",align:"center",width:160,cell:({row:o})=>e.jsxs("div",{className:"flex justify-center gap-4",children:[e.jsx(m,{rowId:o.id,component:()=>i(()=>import("./save-BgdlMpae.js"),__vite__mapDeps([0,1]))}),e.jsx(d,{rowId:o.id})]})}],[t]);return e.jsx(l,{columns:s,table:{rowKey:"id"},title:t("system.role.name"),actionRender:()=>e.jsx(n,{component:()=>i(()=>import("./save-BgdlMpae.js"),__vite__mapDeps([0,1]))})})};export{j as default};
