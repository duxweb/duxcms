function __vite__mapDeps(indexes) {
  if (!__vite__mapDeps.viteFileDeps) {
    __vite__mapDeps.viteFileDeps = ["assets/save-K964yrzi.js","assets/vendor-map-DEYI1At9.js","assets/vendor-react-BlTfyhut.js","assets/vendor-tdesign-6RiSygsd.js","assets/modulepreload-polyfill-qjA-9dc5.js","assets/vendor-echarts-YtT1A1hM.js","assets/vendor-refine-N0YDYeEW.js","assets/vendor-tinymce-DkQhX6vO.js","assets/vendor-markdown-B4Wj7HZg.js","assets/vendor-lib-DKIH9BQe.js"]
  }
  return indexes.map((i) => __vite__mapDeps.viteFileDeps[i])
}
import{_ as l}from"./vendor-markdown-B4Wj7HZg.js";import{j as s}from"./vendor-map-DEYI1At9.js";import{O as d,_ as m}from"./vendor-refine-N0YDYeEW.js";import{Y as x,M as h,Z as j,J as p}from"./modulepreload-polyfill-qjA-9dc5.js";import"./vendor-react-BlTfyhut.js";import"./vendor-echarts-YtT1A1hM.js";import{c as u,O as c,$ as f,N as g,B as v,a8 as o,o as n}from"./vendor-tdesign-6RiSygsd.js";import{C as N,E as _}from"./button-DAYx5G5K.js";import"./vendor-tinymce-DkQhX6vO.js";import"./vendor-lib-DKIH9BQe.js";import"./action-BtJC5SON.js";const R=()=>{const{data:e,refetch:r,loading:a}=x({});return s.jsx(h,{children:s.jsx(u,{loading:a,showOverlay:!0,children:s.jsx("div",{className:"mb-4",children:e&&e.length>0?s.jsx("div",{className:"grid grid-cols-2 gap-4 2xl:grid-cols-4 xl:grid-cols-3",children:e==null?void 0:e.map((t,i)=>s.jsx(y,{item:t,refetch:r},i))}):s.jsx(c,{children:s.jsx(j,{})})})})})},y=({item:e,refetch:r})=>{const{mutate:a}=d(),t=m();return s.jsx(c,{title:e==null?void 0:e.name,actions:s.jsx(f,{theme:"success",children:t("cms.theme.default")}),bordered:!0,cover:s.jsx(g,{src:e==null?void 0:e.image,className:"h-50",fit:"cover",position:"top"}),footer:s.jsxs("div",{className:"grid grid-cols-3 items-center justify-between divide-x divide-gray-200",children:[s.jsx("div",{className:"flex justify-center",children:s.jsx(p,{title:t("cms.theme.info"),trigger:s.jsx(v,{variant:"text",children:s.jsx(o,{content:t("cms.theme.info"),children:s.jsx(n,{name:"help-circle"})})}),children:s.jsx("div",{className:"p-4",children:(e==null?void 0:e.help)||t("cms.theme.empty")})})}),s.jsx("div",{className:"flex justify-center",children:s.jsx(N,{onConfirm:()=>{a({url:`cms/theme/${e==null?void 0:e.id}`,method:"patch",values:{},successNotification:()=>(r==null||r(),!1)})},variant:"text",action:"store",children:s.jsx(o,{content:t("cms.theme.change"),children:s.jsx(n,{name:"component-switch"})})})}),s.jsx("div",{className:"flex justify-center",children:s.jsx(_,{component:()=>l(()=>import("./save-K964yrzi.js"),__vite__mapDeps([0,1,2,3,4,5,6,7,8,9])),rowId:e==null?void 0:e.id,title:t("cms.theme.config"),variant:"text",theme:"default",children:s.jsx(o,{content:t("cms.theme.config"),children:s.jsx(n,{name:"edit-1"})})})})]})})};export{R as default};