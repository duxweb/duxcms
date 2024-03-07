function __vite__mapDeps(indexes) {
  if (!__vite__mapDeps.viteFileDeps) {
    __vite__mapDeps.viteFileDeps = ["assets/save-CI5zz21A.js","assets/modulepreload-polyfill-DkiKXnSB.js","assets/uploadImageManage-DyY6Ak7P.js","assets/manage-Bom0epIq.js","assets/useUpload-DH6oH64-.js","assets/index-BEGpCFfB.js"]
  }
  return indexes.map((i) => __vite__mapDeps.viteFileDeps[i])
}
import{_ as r}from"./index-CInEZ-VQ.js";import{X as s,s as a,j as t,a7 as l}from"./modulepreload-polyfill-DkiKXnSB.js";import{b as n}from"./button-D0ULnvDG.js";import{a as c,D as d}from"./link-JNlAaly3.js";import"./action-BYcpOc6q.js";import"./index-BEGpCFfB.js";const j=()=>{const e=s(),i=a.useMemo(()=>[{colKey:"id",sorter:!0,sortType:"all",title:"ID",width:100},{colKey:"name",title:e("content.source.fields.name"),ellipsis:!0},{colKey:"created_at",title:e("content.source.fields.createdAt"),sorter:!0,sortType:"all",width:200},{colKey:"link",title:e("table.actions"),fixed:"right",align:"center",width:120,cell:({row:o})=>t.jsxs("div",{className:"flex justify-center gap-4",children:[t.jsx(c,{rowId:o.id,component:()=>r(()=>import("./save-CI5zz21A.js"),__vite__mapDeps([0,1,2,3,4,5]))}),t.jsx(d,{rowId:o.id})]})}],[e]);return t.jsx(l,{columns:i,actionRender:()=>t.jsx(n,{component:()=>r(()=>import("./save-CI5zz21A.js"),__vite__mapDeps([0,1,2,3,4,5]))})})};export{j as default};
