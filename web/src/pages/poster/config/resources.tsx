import { App, lazyComponent } from '@duxweb/dux-refine'

export const adminResources = (app: App) => {
  app.addResources([
    {
      name: 'poster.design',
      list: 'poster/design',
      create: 'poster/design/page',
      edit: 'poster/design/page/:id',
      listElenemt: lazyComponent(() => import('../admin/design/list')),
      createElenemt: lazyComponent(() => import('../admin/design/page')),
      editElenemt: lazyComponent(() => import('../admin/design/page')),
      meta: {
        label: 'poster.design',
        icon: 'i-tabler:photo',
        parent: 'tools',
        sort: 100,
      },
    },
  ])
}
