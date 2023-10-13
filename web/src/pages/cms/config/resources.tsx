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
      name: 'cms.theme',
      list: 'cms/theme',
      listElenemt: lazyComponent(() => import('../admin/theme/list')),
      meta: {
        label: 'cms.theme',
        parent: 'cms',
      },
    },
  ])
}
