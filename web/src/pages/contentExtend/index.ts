import { appConfig, appContext } from '@duxweb/dux-refine'
import './hook'

const init = (context: appContext) => {
  const data = import.meta.glob('./locales/*.json', { eager: true })
  context.addI18ns(data)
}

const config: appConfig = {
  init: init,
}

export default config
