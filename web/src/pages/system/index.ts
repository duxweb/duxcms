import { appConfig, appContext, createApp, lazyComponent } from '@duxweb/dux-refine'

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

  admin.addIndexs([
    {
      name: 'system',
      component: lazyComponent(() => import('./admin/total/index')),
    },
  ])

  if (context.config.indexName != 'system') {
    admin.addResources([
      {
        name: 'system.total',
        list: 'system/total',
        listElenemt: lazyComponent(() => import('./admin/total/index')),
        meta: {
          label: 'system.total',
          parent: 'system',
          icon: 'i-tabler:graph',
          sort: 0,
        },
      },
    ])
  }

  adminRouter(admin)
  adminResources(admin, context)
}

const config: appConfig = {
  init: init,
  run: register,
}

export default config
