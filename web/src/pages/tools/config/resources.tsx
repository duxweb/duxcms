import { App, lazyComponent } from '@duxweb/dux-refine'

export const adminResources = (app: App) => {
  app.addResources([
    {
      name: 'tools',
      meta: {
        label: 'tools',
        sort: 90,
      },
    },
    {
      name: 'tools.dataGroup',
      meta: {
        label: 'tools.dataGroup',
        icon: 'system-application',
        parent: 'tools',
      },
    },
    {
      name: 'tools.area',
      list: 'tools/area',
      listElenemt: lazyComponent(() => import('../admin/area/list')),
      meta: {
        label: 'tools.area',
        parent: 'tools.dataGroup',
      },
    },
    {
      name: 'data',
      meta: {
        label: 'data',
        sort: 800,
      },
    },
    {
      name: 'tools.magic',
      list: 'tools/magic',
      create: 'tools/magic/page',
      edit: 'tools/magic/page/:id',
      listElenemt: lazyComponent(() => import('../admin/magic/list')),
      createElenemt: lazyComponent(() => import('../admin/magic/page')),
      editElenemt: lazyComponent(() => import('../admin/magic/page')),
      meta: {
        label: 'tools.magic',
        icon: 'system-sum',
        parent: 'data',
        sort: 0,
      },
    },
    {
      name: 'tools.magicGroup',
      meta: {
        label: 'tools.magicGroup',
        icon: 'system-application',
        parent: 'data',
      },
    },
    {
      name: 'tools.data',
      list: 'data/:name',
      create: 'data/:name/page',
      edit: 'data/:name/page/:id',
      listElenemt: lazyComponent(() => import('../admin/data/list')),
      createElenemt: lazyComponent(() => import('../admin/data/page')),
      editElenemt: lazyComponent(() => import('../admin/data/page')),
      meta: {
        hide: true,
        parent: 'tools.magicGroup',
      },
    },
    {
      name: 'tools.magicGroup',
    },
    {
      name: 'tools.dataPage',
    },
    {
      name: 'tools.file',
      list: 'tools/file',
      listElenemt: lazyComponent(() => import('../admin/file/list')),
      meta: {
        label: 'tools.file',
        icon: 'link',
        parent: 'tools',
      },
    },
  ])
}
