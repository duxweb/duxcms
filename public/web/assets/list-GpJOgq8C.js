function __vite__mapDeps(indexes) {
  if (!__vite__mapDeps.viteFileDeps) {
    __vite__mapDeps.viteFileDeps = ["assets/save-BQWvPDtZ.js","assets/vendor-map-CavGn2Gm.js","assets/vendor-react-BlTfyhut.js","assets/vendor-tdesign-Dl0n85Ru.js","assets/vendor-refine-N0YDYeEW.js","assets/modulepreload-polyfill-gnEWSMTf.js","assets/vendor-echarts-YtT1A1hM.js","assets/vendor-tinymce-DBANSpiv.js","assets/vendor-markdown-CMNXrCXk.js","assets/vendor-lib-CW-uXUk2.js","assets/uploadImageManage-DwC_BrwN.js","assets/manage-FAbkdLmn.js","assets/useUpload-K1yBkspY.js","assets/group-Dj6k452R.js"]
  }
  return indexes.map((i) => __vite__mapDeps.viteFileDeps[i])
}
import{_ as i}from"./vendor-markdown-CMNXrCXk.js";import{j as e}from"./vendor-map-CavGn2Gm.js";import{R as s}from"./vendor-react-BlTfyhut.js";import{_ as a}from"./vendor-refine-N0YDYeEW.js";import{W as d}from"./modulepreload-polyfill-gnEWSMTf.js";import"./vendor-echarts-YtT1A1hM.js";import{F as l}from"./vendor-tdesign-Dl0n85Ru.js";import{F as u}from"./filterEdit-OFxBAhBh.js";import"./vendor-tinymce-DBANSpiv.js";import"./vendor-lib-CW-uXUk2.js";import{b as c}from"./button-BpZyQvx9.js";import{a as p,D as _}from"./link-Cl8VaVKv.js";import"./action-CogfU8lF.js";const v=()=>{const t=a(),[r]=l.useForm(),n=l.useWatch("menu_id",r),m=s.useMemo(()=>[{colKey:"id",sorter:!0,sortType:"all",title:"ID",width:150},{colKey:"title",title:t("content.menu.fields.title"),ellipsis:!0},{colKey:"url",title:t("content.menu.fields.url"),ellipsis:!0},{colKey:"link",title:t("table.actions"),fixed:"right",align:"center",width:160,cell:({row:o})=>e.jsxs("div",{className:"flex justify-center gap-4",children:[e.jsx(p,{rowId:o.id,component:()=>i(()=>import("./save-BQWvPDtZ.js"),__vite__mapDeps([0,1,2,3,4,5,6,7,8,9,10,11,12])),componentProps:{menu_id:o.menu_id}}),e.jsx(_,{rowId:o.id,params:{menu_id:o.menu_id}})]})}],[t]);return e.jsx(e.Fragment,{children:e.jsx(d,{columns:m,filterForm:r,table:{rowKey:"id",tree:{childrenKey:"children",treeNodeColumnIndex:1,defaultExpandAll:!0},pagination:void 0},actionRender:()=>e.jsx(e.Fragment,{children:n&&e.jsx(c,{title:t("buttons.create"),component:()=>i(()=>import("./save-BQWvPDtZ.js"),__vite__mapDeps([0,1,2,3,4,5,6,7,8,9,10,11,12])),componentProps:{menu_id:n}})}),filterRender:()=>e.jsx(e.Fragment,{children:e.jsx(u,{title:t("content.menu.placeholder.group"),resource:"content.menu",form:r,field:"menu_id",defaultSelect:!0,optionLabel:"name",optionValue:"id",component:()=>i(()=>import("./group-Dj6k452R.js"),__vite__mapDeps([13,1,2,3,4,5,6,7,8,9]))})})})})};export{v as default};
