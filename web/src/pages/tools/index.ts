import { appConfig, appContext } from '@duxweb/dux-refine'
import { adminResources } from './config/resources'

import enUSLang from './locales/en-US/common.json'
import zhCNLang from './locales/zh-CN/common.json'
import zhTWLang from './locales/zh-TW/common.json'
import jaJPLang from './locales/ja-JP/common.json'
import koKRLang from './locales/ko-KR/common.json'
import ruRULang from './locales/ru-RU/common.json'

import enUSExtendLang from '@duxweb/dux-extend/dist/locales/en_US/extend.json'
import zhCNExtendLang from '@duxweb/dux-extend/dist/locales/zh_CN/extend.json'
import zhTWExtendLang from '@duxweb/dux-extend/dist/locales/zh_TW/extend.json'
import jaJPExtendLang from '@duxweb/dux-extend/dist/locales/ja_JP/extend.json'
import koKRExtendLang from '@duxweb/dux-extend/dist/locales/ko_KR/extend.json'
import ruRUExtendLang from '@duxweb/dux-extend/dist/locales/ru_RU/extend.json'

const init = (context: appContext) => {
  context.addI18n('en-US', 'common', enUSLang)
  context.addI18n('zh-CN', 'common', zhCNLang)
  context.addI18n('zh-TW', 'common', zhTWLang)
  context.addI18n('ja-JP', 'common', jaJPLang)
  context.addI18n('ko-KR', 'common', koKRLang)
  context.addI18n('ru-RU', 'common', ruRULang)
  context.addI18n('en-US', 'extend', enUSExtendLang)
  context.addI18n('zh-CN', 'extend', zhCNExtendLang)
  context.addI18n('zh-TW', 'extend', zhTWExtendLang)
  context.addI18n('ja-JP', 'extend', jaJPExtendLang)
  context.addI18n('ko-KR', 'extend', koKRExtendLang)
  context.addI18n('ru-RU', 'extend', ruRUExtendLang)
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
