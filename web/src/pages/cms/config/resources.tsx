import { App, lazyComponent } from '@duxweb/dux-refine'

export const adminResources = (app: App) => {
  app.addResources([
    {
      name: 'cms.stats.index',
      list: 'system/index',
      listElenemt: lazyComponent(() => import('../admin/home/index')),
      meta: {
        label: 'cms.dashboard',
        icon: 'home',
        sort: 0,
      },
    },
    {
      name: 'cms',
      meta: {
        label: 'cms',
        icon: 'system-application',
        parent: 'system',
        sort: 50,
      },
    },
    {
      name: 'cms.setting',
      list: 'cms/setting',
      listElenemt: lazyComponent(() => import('../admin/setting/page')),
      meta: {
        label: 'cms.setting',
        parent: 'cms',
      },
    },
    {
      name: 'cms.setting',
      list: 'cms/setting',
      listElenemt: lazyComponent(() => import('../admin/setting/page')),
      meta: {
        label: 'cms.setting',
        parent: 'cms',
      },
    },
    {
      name: 'cms.template',
      list: 'cms/template',
      listElenemt: lazyComponent(() => import('../admin/template/list')),
      meta: {
        label: 'cms.template',
        parent: 'cms',
      },
    },
  ])
}
