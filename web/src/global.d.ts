import { lang, siderType } from '@duxweb/dux-refine'

declare global {
  interface Window {
    lang?: lang
    sideType?: siderType
  }
}

declare module '*.svg' {
  const content: any
  export default content
}
