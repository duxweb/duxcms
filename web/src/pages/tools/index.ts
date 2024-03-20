import { appConfig, appContext } from '@duxweb/dux-refine'
import { adminResources } from './config/resources'

const init = (context: appContext) => {
  const data = import.meta.glob('./locales/*.json', { eager: true })
  context.addI18ns(data)

  const dataExtend = import.meta.glob('@/../node_modules/@duxweb/dux-extend/dist/locales/*.json', {
    eager: true,
  })
  context.addI18ns(dataExtend)
}

const register = (context: appContext) => {
  const admin = context.getApp('admin')
  adminResources(admin)
}

const config: appConfig = {
  init: init,
  register: register,
}

export default config
