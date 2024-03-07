import { lang, siderType } from '@duxweb/dux-refine'

declare global {
  interface Window {
    lang?: lang
    sideType?: siderType
    baiduMap?: string
  }
}

declare module '*.svg' {
  const content: any
  export default content
}
