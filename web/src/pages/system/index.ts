import { appConfig, appContext, createApp } from '@duxweb/dux-refine'

import { adminRouter } from './config/router'
import { adminResources } from './config/resources'
import zhCNLang from './locales/zh-CN/common.json'
import enUSLang from './locales/en-US/common.json'

// init function will be called when app start
const init = (context: appContext) => {
  context.createApp('admin', createApp())
  context.addI18n('zh-CN', 'common', zhCNLang)
  context.addI18n('en-US', 'common', enUSLang)
  return null
}

// register function will be called when app register
const register = (context: appContext) => {
  const admin = context.getApp('admin')

  adminRouter(admin)
  adminResources(admin)

  admin.setUserMenu([
    {
      label: 'setting',
      icon: 'i-tabler:home',
      route: 'index',
    },
  ])
}

const config: appConfig = {
  init: init,
  register: register,
}

export default config
