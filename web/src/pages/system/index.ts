import { appConfig, appContext, createApp } from '@duxweb/dux-refine'

import { adminRouter } from './config/router'
import { adminResources } from './config/resources'
import enUSLang from './locales/en-US/common.json'
import zhCNLang from './locales/zh-CN/common.json'
import zhTWLang from './locales/zh-TW/common.json'
import jaJPLang from './locales/ja-JP/common.json'
import koKRLang from './locales/ko-KR/common.json'
import ruRULang from './locales/ru-RU/common.json'

// init function will be called when app start
const init = (context: appContext) => {
  context.createApp('admin', createApp())
  context.addI18n('en-US', 'common', enUSLang)
  context.addI18n('zh-CN', 'common', zhCNLang)
  context.addI18n('zh-TW', 'common', zhTWLang)
  context.addI18n('ja-JP', 'common', jaJPLang)
  context.addI18n('ko-KR', 'common', koKRLang)
  context.addI18n('ru-RU', 'common', ruRULang)
  return null
}

// register function will be called when app register
const register = (context: appContext) => {
  const admin = context.getApp('admin')

  adminRouter(admin)
  adminResources(admin)
}

const config: appConfig = {
  init: init,
  register: register,
}

export default config
