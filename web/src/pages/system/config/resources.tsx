import { App, appContext, lazyComponent } from '@duxweb/dux-refine'

export const adminResources = (app: App, context: appContext) => {
  const indexs = app.getIndexs()

  const info = indexs.find((item) => {
    if (item.name == context.config.indexName) {
      return true
    }
    return false
  })

  app.addResources([
    {
      name: 'admin',
      list: 'system/index',
      listElenemt: info?.component,
      meta: {
        label: 'system.stats',
        icon: 'i-tabler:home',
        sort: 0,
      },
    },
    {
      name: 'system',
      meta: {
        label: 'system',
        sort: 1000,
        icon: 'i-tabler:settings',
      },
    },
    {
      name: 'system.api',
      list: 'system/api',
      listElenemt: lazyComponent(() => import('../admin/api/list')),
      meta: {
        label: 'system.api',
        parent: 'system',
        icon: 'i-tabler:api',
        sort: 100,
      },
    },
    {
      name: 'system.userGroup',
      meta: {
        label: 'system.userGroup',
        parent: 'system',
        icon: 'i-tabler:users',
        sort: 101,
      },
    },
    {
      name: 'system.user',
      list: 'system/user',
      listElenemt: lazyComponent(() => import('../admin/user/list')),
      meta: {
        label: 'system.user',
        parent: 'system.userGroup',
      },
    },
    {
      name: 'system.role',
      list: 'system/role',
      listElenemt: lazyComponent(() => import('../admin/role/list')),
      meta: {
        label: 'system.role',
        parent: 'system.userGroup',
      },
    },
    {
      name: 'system.logGroup',
      meta: {
        label: 'system.logGroup',
        parent: 'system',
        icon: 'i-tabler:list',
      },
    },
    {
      name: 'system.operate',
      list: 'system/operate',
      listElenemt: lazyComponent(() => import('../admin/operate/list')),
      meta: {
        label: 'system.operate',
        parent: 'system.logGroup',
      },
    },
  ])
}
