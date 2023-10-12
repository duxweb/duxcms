import { App, lazyComponent } from '@duxweb/dux-refine'

export const adminResources = (app: App) => {
  app.addResources([
    {
      name: 'system',
      meta: {
        label: 'system',
        sort: 1000,
      },
    },
    {
      name: 'system.api',
      list: 'system/api',
      listElenemt: lazyComponent(() => import('../admin/api/list')),
      meta: {
        label: 'system.api',
        parent: 'system',
        icon: 'api',
      },
    },
    {
      name: 'system.userGroup',
      meta: {
        label: 'system.userGroup',
        parent: 'system',
        icon: 'user',
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
        icon: 'certificate',
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
