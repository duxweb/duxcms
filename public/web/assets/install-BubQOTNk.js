import{j as s}from"./vendor-map-CavGn2Gm.js";import{r as l}from"./vendor-react-BlTfyhut.js";import{_ as g}from"./vendor-refine-N0YDYeEW.js";import{H as j,I as f}from"./modulepreload-polyfill-gnEWSMTf.js";import"./vendor-echarts-YtT1A1hM.js";import{G as v,I as b,a2 as N,B as S,c as C}from"./vendor-tdesign-Dl0n85Ru.js";import"./vendor-tinymce-DBANSpiv.js";import"./vendor-lib-CW-uXUk2.js";import"./vendor-markdown-CMNXrCXk.js";const q=()=>{const e=g(),[r,d]=l.useState(""),[n,u]=l.useState(!1),p=j(),[m,i]=l.useState(!1),[o,x]=l.useState(""),h=l.useCallback(a=>{i(!0),p.request("cloud/apps/install","post",{data:{url:a,build:n}}).then(t=>{var c;if(t.code!==200){v.error(t.message);return}x((c=t==null?void 0:t.data)==null?void 0:c.content)}).finally(()=>{i(!1)})},[]);return s.jsxs(s.Fragment,{children:[s.jsxs("div",{className:"p-4",children:[s.jsx("div",{className:"mb-2",children:e("cloud.apps.validator.url")}),s.jsx(b,{value:r,placeholder:"dux://",disabled:!!o,onChange:a=>{d(()=>a)}})]}),s.jsxs("div",{className:"p-4",children:[s.jsx("div",{className:"mb-2",children:e("cloud.apps.validator.build")}),s.jsx(N,{value:n,onChange:a=>u(a)}),s.jsx("div",{className:"mt-2 text-placeholder",children:e("cloud.apps.help.build")})]}),o?s.jsx("div",{className:"p-4",children:s.jsx("pre",{className:"overflow-auto rounded-lg p-4 bg-component",children:o})}):s.jsx(f.Footer,{children:s.jsx(S,{onClick:()=>{h(r)},children:e("cloud.apps.action.install")})}),s.jsx(C,{loading:m,fullscreen:!0,preventScrollThrough:!0,text:e("cloud.apps.tips.loading")})]})};export{q as default};
