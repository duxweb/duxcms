function __vite__mapDeps(indexes) {
  if (!__vite__mapDeps.viteFileDeps) {
    __vite__mapDeps.viteFileDeps = ["assets/save-Dv0RgjeH.js","assets/vendor-map-xrlPGoMd.js","assets/vendor-react-CEAiij2-.js","assets/vendor-tdesign-CDUa2Lvz.js","assets/vendor-refine-BPS4QmKj.js","assets/modulepreload-polyfill-DuwZr_J0.js","assets/vendor-echarts-BA2V57Gb.js","assets/vendor-tinymce-DBANSpiv.js","assets/vendor-markdown-BG3k-yNs.js","assets/vendor-lib-DkJHNO_p.js","assets/uploadImageManage-DuSKSKW2.js","assets/manage-DqBD0yRD.js","assets/useUpload-DAFBiD59.js","assets/group-CX_bmJeJ.js"]
  }
  return indexes.map((i) => __vite__mapDeps.viteFileDeps[i])
}
import{_ as i}from"./vendor-markdown-BG3k-yNs.js";import{j as e}from"./vendor-map-xrlPGoMd.js";import{A as s}from"./vendor-react-CEAiij2-.js";import{_ as d}from"./vendor-refine-BPS4QmKj.js";import{P as a}from"./modulepreload-polyfill-DuwZr_J0.js";import"./vendor-echarts-BA2V57Gb.js";import{F as l}from"./vendor-tdesign-CDUa2Lvz.js";import{F as u}from"./filterEdit-CLuTwvAm.js";import"./vendor-tinymce-DBANSpiv.js";import"./vendor-lib-DkJHNO_p.js";import{b as c}from"./button-BG1z6bng.js";import{a as p,D as _}from"./link-Bwn5aYx5.js";import"./action-Dcsrtdbk.js";const A=()=>{const t=d(),[r]=l.useForm(),n=l.useWatch("menu_id",r),m=s.useMemo(()=>[{colKey:"id",sorter:!0,sortType:"all",title:"ID",width:150},{colKey:"title",title:t("content.menu.fields.title"),ellipsis:!0},{colKey:"url",title:t("content.menu.fields.url"),ellipsis:!0},{colKey:"link",title:t("table.actions"),fixed:"right",align:"center",width:160,cell:({row:o})=>e.jsxs("div",{className:"flex justify-center gap-4",children:[e.jsx(p,{rowId:o.id,component:()=>i(()=>import("./save-Dv0RgjeH.js"),__vite__mapDeps([0,1,2,3,4,5,6,7,8,9,10,11,12])),componentProps:{menu_id:o.menu_id}}),e.jsx(_,{rowId:o.id,params:{menu_id:o.menu_id}})]})}],[t]);return e.jsx(e.Fragment,{children:e.jsx(a,{columns:m,filterForm:r,table:{rowKey:"id",tree:{childrenKey:"children",treeNodeColumnIndex:1,defaultExpandAll:!0},pagination:void 0},actionRender:()=>e.jsx(e.Fragment,{children:n&&e.jsx(c,{title:t("buttons.create"),component:()=>i(()=>import("./save-Dv0RgjeH.js"),__vite__mapDeps([0,1,2,3,4,5,6,7,8,9,10,11,12])),componentProps:{menu_id:n}})}),filterRender:()=>e.jsx(e.Fragment,{children:e.jsx(u,{title:t("content.menu.placeholder.group"),resource:"content.menu",form:r,field:"menu_id",defaultSelect:!0,optionLabel:"name",optionValue:"id",component:()=>i(()=>import("./group-CX_bmJeJ.js"),__vite__mapDeps([13,1,2,3,4,5,6,7,8,9]))})})})})};export{A as default};