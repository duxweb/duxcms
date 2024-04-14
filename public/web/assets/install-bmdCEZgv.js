import{j as e}from"./vendor-map-DEYI1At9.js";import{j as I,k as T,m as L,n as C,z as w,o as F,p as y,q as U,r as B,h as H,D as k,i as A,s as z}from"./modulepreload-polyfill-qjA-9dc5.js";import{r as m}from"./vendor-react-BlTfyhut.js";import"./vendor-echarts-YtT1A1hM.js";import{S as c,B as p,D as E,b as O,L as f,F as j,I as g,A as v,c as P,e as W,f as b,g as M,h as Y,i as G,j as K}from"./vendor-tdesign-6RiSygsd.js";import"./vendor-tinymce-DkQhX6vO.js";import"./vendor-lib-DKIH9BQe.js";import"./index-DiPqZObD.js";import"./vendor-refine-N0YDYeEW.js";import"./vendor-markdown-B4Wj7HZg.js";import"./useSelect-COj1vjfo.js";import"./cascader-DEwlaN_5.js";import"./uploadFile-xuCkkb-U.js";import"./useUpload-DYrPesVj.js";import"./tinymce-5bImtXzI.js";import"./manage-DyrHxbtH.js";const J=({setTab:o})=>{const{t:x,i18n:u}=I(),d=u.language;return e.jsxs(e.Fragment,{children:[e.jsx("div",{className:"bg-component p-4 pt-1",children:e.jsxs("div",{className:"max-w-full prose",children:[e.jsx("p",{className:"text-center text-lg font-bold",children:"MIT License Copyright (c) 2023 duxweb"}),e.jsx("p",{children:'Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:'}),e.jsx("p",{children:"The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software."}),e.jsx("p",{children:'THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.'})]})}),e.jsxs(N,{children:[e.jsx("div",{children:e.jsxs(c,{value:d,onChange:t=>{u.changeLanguage(t)},children:[e.jsx(c.Option,{value:"en-US",children:"English"}),e.jsx(c.Option,{value:"zh-CN",children:"简体中文"}),e.jsx(c.Option,{value:"zh-TW",children:"繁体中文"}),e.jsx(c.Option,{value:"ja-JP",children:"日本語"}),e.jsx(c.Option,{value:"ko-KR",children:"한국어"}),e.jsx(c.Option,{value:"ru-RU",children:"русск"})]})}),e.jsx(p,{theme:"default",onClick:()=>{window.alert(x("install.close",{ns:"extend"}))},children:x("install.disagree",{ns:"extend"})}),e.jsx(p,{onClick:()=>{o(1)},children:x("install.agree",{ns:"extend"})})]})]})},V=({setTab:o,config:x,path:u})=>{const{t:d}=I(),[t,h]=m.useState({});m.useEffect(()=>{T.get((x.apiUrl||"")+u).then(s=>{var a;h((a=s==null?void 0:s.data)==null?void 0:a.data)})},[]);const l=t==null?void 0:t.ext,n=t==null?void 0:t.packages,i=(t==null?void 0:t.status)||!1;return e.jsxs(e.Fragment,{children:[e.jsx(E,{align:"center",layout:"horizontal",children:d("install.pakcage",{ns:"extend"})}),e.jsx(O,{loading:!n||!(n!=null&&n.length),theme:"paragraph",children:e.jsx(f,{children:n==null?void 0:n.map((s,a)=>e.jsx(f.ListItem,{action:e.jsx("div",{className:"text-gray-6",children:s==null?void 0:s.ver}),children:s==null?void 0:s.name},a))})}),e.jsx(E,{align:"center",layout:"horizontal",children:d("install.extend",{ns:"extend"})}),e.jsx(O,{loading:!l||!(l!=null&&l.length),theme:"paragraph",children:e.jsx(f,{children:l==null?void 0:l.map((s,a)=>e.jsx(f.ListItem,{action:e.jsx("div",{className:L(["h-4 w-4 flex items-center justify-center rounded-full p-1 text-white ",s!=null&&s.status?"bg-success":"bg-error"]),children:s!=null&&s.status?e.jsx("div",{className:"i-tabler:check"}):e.jsx("div",{className:"i-tabler:x"})}),children:s==null?void 0:s.name},a))})}),e.jsxs(N,{children:[e.jsx(p,{theme:"default",onClick:()=>{o(0)},children:d("install.prev",{ns:"extend"})}),e.jsx(p,{disabled:!i,onClick:()=>{o(2)},children:d("install.next",{ns:"extend"})})]})]})},q=({setTab:o,setData:x,config:u,path:d})=>{const{t,i18n:h}=I(),l=h.language,[n,i]=m.useState({}),s=a=>{T.post((u.apiUrl||"")+d,a.fields).then(r=>{var D,S;i((D=r==null?void 0:r.data)==null?void 0:D.data);const R=(S=r==null?void 0:r.data)==null?void 0:S.data;R!=null&&R.error||(o(3),x(a.fields))}).catch(r=>{console.log(r),i({error:!0,message:(r==null?void 0:r.message)||"Unknown"})})};return e.jsx(e.Fragment,{children:e.jsxs(j,{onSubmit:s,children:[e.jsx(E,{align:"center",layout:"horizontal",children:t("install.system",{ns:"extend"})}),e.jsx(j.FormItem,{initialData:l,label:t("install.systemLang",{ns:"extend"}),name:["use","lang"],children:e.jsxs(c,{children:[e.jsx(c.Option,{value:"en-US",children:"English"}),e.jsx(c.Option,{value:"zh-CN",children:"简体中文"}),e.jsx(c.Option,{value:"zh-TW",children:"繁体中文"}),e.jsx(c.Option,{value:"ja-JP",children:"日本語"}),e.jsx(c.Option,{value:"ko-KR",children:"한국어"}),e.jsx(c.Option,{value:"ru-RU",children:"русск"})]})}),e.jsx(j.FormItem,{initialData:"Dux",label:t("install.systemName",{ns:"extend"}),name:["use","name"],children:e.jsx(g,{})}),e.jsx(j.FormItem,{initialData:"http://localhost",label:t("install.systemDomain",{ns:"extend"}),name:["use","domain"],children:e.jsx(g,{})}),e.jsx(E,{align:"center",layout:"horizontal",children:t("install.database",{ns:"extend"})}),(n==null?void 0:n.error)&&e.jsx("div",{className:"mb-6",children:e.jsx(v,{theme:"error",message:n==null?void 0:n.message})}),e.jsx(j.FormItem,{initialData:"localhost",label:t("install.databaseHost",{ns:"extend"}),name:["database","host"],children:e.jsx(g,{})}),e.jsx(j.FormItem,{initialData:"dux",label:t("install.databaseName",{ns:"extend"}),name:["database","name"],children:e.jsx(g,{})}),e.jsx(j.FormItem,{initialData:"root",label:t("install.databaseUsername",{ns:"extend"}),name:["database","username"],children:e.jsx(g,{})}),e.jsx(j.FormItem,{initialData:"",label:t("install.databasePassword",{ns:"extend"}),name:["database","password"],children:e.jsx(g,{})}),e.jsx(j.FormItem,{initialData:"3306",label:t("install.databasePort",{ns:"extend"}),name:["database","port"],children:e.jsx(g,{})}),e.jsxs(N,{children:[e.jsx(p,{theme:"default",onClick:()=>{o(1)},children:t("install.prev",{ns:"extend"})}),e.jsx(p,{type:"submit",children:t("install.next",{ns:"extend"})})]})]})})},X=({config:o,data:x,path:u})=>{const[d,t]=m.useState(!0),[h,l]=m.useState(!1),[n,i]=m.useState({}),{t:s}=I();return m.useEffect(()=>{T.post((o.apiUrl||"")+u,x).then(a=>{var r;i((r=a.data)==null?void 0:r.data),t(!1),l(!0)}).catch(a=>{console.log(a),i({error:!0,message:(a==null?void 0:a.message)||"Unknown"})})},[]),e.jsxs(e.Fragment,{children:[e.jsx(P,{size:"small",loading:d,showOverlay:!0,text:s("install.loading",{ns:"extend"}),children:e.jsx("div",{className:"",children:e.jsx(O,{theme:"paragraph",animation:"flashed",loading:d,children:n!=null&&n.error?e.jsx(v,{theme:"error",message:n==null?void 0:n.message}):e.jsx("div",{className:"bg-component p-4",children:e.jsx("pre",{children:n.logs})})})})}),h&&e.jsx(N,{children:e.jsx(p,{onClick:()=>{window.location.href="/manage"},children:s("install.login",{ns:"extend"})})})]})},$="/web/assets/background-DLLzPY35.svg",Q=({config:o,logo:x,pathDetection:u,pathConfig:d,pathComplete:t})=>{const[h,l]=m.useState(0),{t:n,i18n:i}=I(),[s,a]=m.useState({});return m.useEffect(()=>{i.addResourceBundle("en-US","extend",C),i.addResourceBundle("zh-CN","extend",w),i.addResourceBundle("zh-TW","extend",F),i.addResourceBundle("ja-JP","extend",y),i.addResourceBundle("ko-KR","extend",U),i.addResourceBundle("ru-RU","extend",B),i.changeLanguage(i.language)},[]),e.jsx("div",{className:"h-screen w-screen overflow-x-hidden overflow-y-auto flex justify-center bg-repeat bg-page",style:{backgroundImage:`url(${$})`},children:e.jsxs("div",{className:"flex flex-col items-center",children:[e.jsx("div",{className:"mt-8",children:x}),e.jsxs("div",{className:"m-8 max-w-full w-200 rounded-lg p-10 shadow bg-container",children:[e.jsxs(W,{current:h,children:[e.jsx(b,{icon:e.jsx(M,{}),title:n("install.agreement",{ns:"extend"}),content:n("install.agreementDesc",{ns:"extend"})}),e.jsx(b,{icon:e.jsx(Y,{}),title:n("install.detection",{ns:"extend"}),content:n("install.detectionDesc",{ns:"extend"})}),e.jsx(b,{icon:e.jsx(G,{}),title:n("install.config",{ns:"extend"}),content:n("install.configDesc",{ns:"extend"})}),e.jsx(b,{icon:e.jsx(K,{}),title:n("install.complete",{ns:"extend"}),content:n("install.completeDesc",{ns:"extend"})})]}),e.jsx("div",{className:"mt-10",children:e.jsxs("div",{children:[h===0&&e.jsx(J,{setTab:l}),h===1&&e.jsx(V,{setTab:l,config:o,path:u}),h===2&&e.jsx(q,{setTab:l,config:o,setData:a,path:d}),h===3&&e.jsx(X,{setTab:l,config:o,data:s,path:t})]})})]})]})})},N=({children:o})=>e.jsx("div",{className:"mt-4 flex flex justify-end gap-2",children:o}),Z=document.getElementById("root"),_=H(Z);_.render(e.jsx(k,{config:A,children:e.jsx(Q,{config:A,pathDetection:"/install/detection",pathConfig:"/install/config",pathComplete:"/install/complete",logo:e.jsx(z,{className:"h-10"})})}));