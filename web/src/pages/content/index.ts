import { appConfig, appContext } from '@duxweb/dux-refine'

import { adminResources } from './config/resources'
import zhCNLang from './locales/zh-CN/common.json'
import enUSLang from './locales/en-US/common.json'

const init = (context: appContext) => {
  context.addI18n('zh-CN', 'common', zhCNLang)
  context.addI18n('en-US', 'common', enUSLang)
  return null
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
