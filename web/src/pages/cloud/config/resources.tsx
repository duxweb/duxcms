import { App, lazyComponent } from '@duxweb/dux-refine'

export const adminResources = (app: App) => {
  app.addResources([
    {
      name: 'cloud.apps',
      list: 'cloud/apps',
      listElenemt: lazyComponent(() => import('../admin/apps/list')),
      meta: {
        icon: 'app',
        label: 'cloud.apps',
        parent: 'system',
        sort: 1000,
      },
    },
  ])
}
