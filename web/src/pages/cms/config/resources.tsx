import { App, lazyComponent } from '@duxweb/dux-refine'

export const adminResources = (app: App) => {
  app.addResources([
    {
      name: 'cms',
      meta: {
        label: 'cms',
        icon: 'i-tabler:sitemap',
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
    {
      name: 'cms.setting',
      list: 'cms/setting',
      listElenemt: lazyComponent(() => import('../admin/setting/page')),
      meta: {
        label: 'cms.setting',
        parent: 'cms',
      },
    },
  ])
}
