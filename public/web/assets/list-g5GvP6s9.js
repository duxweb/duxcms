function __vite__mapDeps(indexes) {
  if (!__vite__mapDeps.viteFileDeps) {
    __vite__mapDeps.viteFileDeps = ["assets/import-DC_mgbnj.js","assets/modulepreload-polyfill-DkiKXnSB.js","assets/useUpload-DH6oH64-.js"]
  }
  return indexes.map((i) => __vite__mapDeps.viteFileDeps[i])
}
import{_ as i}from"./index-CInEZ-VQ.js";import{X as s,s as r,j as t,a7 as a}from"./modulepreload-polyfill-DkiKXnSB.js";import{B as n}from"./button-D0ULnvDG.js";import{D as c}from"./link-JNlAaly3.js";import"./action-BYcpOc6q.js";import"./index-BEGpCFfB.js";const _=()=>{const e=s(),o=r.useMemo(()=>[{colKey:"id",sorter:!0,sortType:"all",title:"ID",width:150},{colKey:"code",title:e("tools.area.fields.code"),ellipsis:!0},{colKey:"name",title:e("tools.area.fields.name"),ellipsis:!0},{colKey:"level",title:e("tools.area.fields.level"),ellipsis:!0},{colKey:"link",title:e("table.actions"),fixed:"right",align:"center",width:100,cell:({row:l})=>t.jsx("div",{className:"flex justify-center gap-4",children:t.jsx(c,{rowId:l.id})})}],[e]);return t.jsx(a,{columns:o,table:{rowKey:"id"},title:e("tools.area.name"),actionRender:()=>t.jsx(n,{title:e("buttons.import"),component:()=>i(()=>import("./import-DC_mgbnj.js"),__vite__mapDeps([0,1,2])),action:"import",icon:t.jsx("div",{className:"t-icon i-tabler:plus"})})})};export{_ as default};
