import { appConfig, appContext, createApp } from '@duxweb/dux-refine'

import { adminRouter } from './config/router'
import { adminResources } from './config/resources'

// init function will be called when app start
const init = (context: appContext) => {
  const data = import.meta.glob('./locales/*.json', { eager: true })
  context.addI18ns(data)
  context.createApp('admin', createApp())
  return null
}

// register function will be called when app register
const register = (context: appContext) => {
  const admin = context.getApp('admin')
  adminRouter(admin)
  adminResources(admin, context)
}

const config: appConfig = {
  init: init,
  register: register,
}

export default config
