const __vite__fileDeps=["assets/save-b-zqZpAv.js","assets/vendor-map-DjDVTs0N.js","assets/vendor-react-BlTfyhut.js","assets/vendor-tdesign-BxQoi5Mo.js","assets/vendor-refine-BK41FXO6.js","assets/modulepreload-polyfill-D6ThnDFs.js","assets/vendor-echarts-JHGMnnew.js","assets/vendor-tinymce-lUkVC9Da.js","assets/vendor-markdown-CL7ixDl-.js","assets/vendor-lib-CKVa3D7Q.js","assets/uploadImageManage-HbmYZfmq.js","assets/manage-bOx1Sh-_.js","assets/useUpload-BsrPC7ib.js","assets/useSelect-BorzI4ID.js"],__vite__mapDeps=i=>i.map(i=>__vite__fileDeps[i]);
import{_ as i}from"./vendor-markdown-CL7ixDl-.js";import{j as e}from"./vendor-map-DjDVTs0N.js";import{R as l}from"./vendor-react-BlTfyhut.js";import{G as o}from"./vendor-refine-BK41FXO6.js";import{S as r,T as m,F as n}from"./modulepreload-polyfill-D6ThnDFs.js";import"./vendor-echarts-JHGMnnew.js";import{a0 as d,I as c}from"./vendor-tdesign-BxQoi5Mo.js";import{b as u}from"./button-VrESTZal.js";import{a as p,D as x}from"./link-NY0bYu7g.js";import"./vendor-tinymce-lUkVC9Da.js";import"./vendor-lib-CKVa3D7Q.js";import"./action-CcBf9P9h.js";const g=()=>{const t=o(),a=l.useMemo(()=>[{colKey:"id",sorter:!0,sortType:"all",title:"ID",width:150},{colKey:"nickname",title:t("system.user.fields.nickname"),ellipsis:!0,cell:({row:s})=>e.jsxs(r,{size:"small",children:[e.jsx(r.Avatar,{image:s.avatar,children:s.nickname[0]}),e.jsx(r.Title,{children:s.nickname})]})},{colKey:"username",title:t("system.user.fields.username"),ellipsis:!0},{colKey:"status",title:t("system.user.fields.status"),edit:{component:d,props:{},keepEditMode:!0}},{colKey:"link",title:t("table.actions"),fixed:"right",align:"center",width:160,cell:({row:s})=>e.jsxs("div",{className:"flex justify-center gap-4",children:[e.jsx(p,{rowId:s.id,component:()=>i(()=>import("./save-b-zqZpAv.js"),__vite__mapDeps([0,1,2,3,4,5,6,7,8,9,10,11,12,13]))}),e.jsx(x,{rowId:s.id})]})}],[t]);return e.jsx(m,{columns:a,table:{rowKey:"id"},title:t("system.user.name"),tabs:[{label:t("system.user.tabs.all"),value:"0"},{label:t("system.user.tabs.enabled"),value:"1"},{label:t("system.user.tabs.disabled"),value:"2"}],actionRender:()=>e.jsx(u,{component:()=>i(()=>import("./save-b-zqZpAv.js"),__vite__mapDeps([0,1,2,3,4,5,6,7,8,9,10,11,12,13]))}),filterRender:()=>e.jsx(e.Fragment,{children:e.jsx(n,{name:"keyword",children:e.jsx(c,{})})})})};export{g as default};
