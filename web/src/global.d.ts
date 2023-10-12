import { lang } from '@duxweb/dux-refine'

declare global {
  interface Window {
    lang?: lang
  }
}

declare module '*.svg' {
  const content: any
  export default content
}
