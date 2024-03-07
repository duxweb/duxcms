import { appConfig, appContext, lazyComponent } from '@duxweb/dux-refine'
import { adminResources } from './config/resources'

const init = (context: appContext) => {
  const data = import.meta.glob('./locales/*.json', { eager: true })
  context.addI18ns(data)
}

const register = (context: appContext) => {
  const admin = context.getApp('admin')

  admin.addIndexs([
    {
      name: 'cms',
      component: lazyComponent(() => import('./admin/home/index')),
    },
  ])

  if (context.config.indexName != 'cms') {
    admin.addResources([
      {
        name: 'cms.dashboard',
        list: 'cms/dashboard',
        listElenemt: lazyComponent(() => import('./admin/home/index')),
        meta: {
          label: 'cms.dashboard',
          icon: 'i-tabler:home',
          parent: 'content',
          sort: 0,
        },
      },
    ])
  }
  adminResources(admin)
}

const config: appConfig = {
  init: init,
  register: register,
}

export default config
