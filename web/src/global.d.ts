import { lang, siderType } from '@duxweb/dux-refine'

declare global {
  interface Manage {
    sideType?: siderType
    indexName?: string
    baiduMap?: string
    [key: string]: any
  }
  interface Window {
    lang?: lang
    baiduMap?: string
    manage?: Manage
  }
}

declare module '*.svg' {
  const content: any
  export default content
}
