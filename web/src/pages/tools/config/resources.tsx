import { App, lazyComponent } from '@duxweb/dux-refine'

export const adminResources = (app: App) => {
  app.addResources([
    {
      name: 'tools',
      meta: {
        label: 'tools',
        sort: 90,
        icon: 'i-tabler:tools',
      },
    },
    {
      name: 'tools.dataGroup',
      meta: {
        label: 'tools.dataGroup',
        icon: 'i-tabler:database',
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
      name: 'tools.backup',
      list: 'tools/backup',
      listElenemt: lazyComponent(() => import('../admin/backup/list')),
      meta: {
        label: 'tools.backup',
        parent: 'tools.dataGroup',
      },
    },
    {
      name: 'data',
      meta: {
        label: 'data',
        sort: 800,
        icon: 'i-tabler:database',
      },
    },
    {
      name: 'tools.magicManage',
      meta: {
        label: 'tools.magicManage',
        sort: 800,
        icon: 'i-tabler:template',
        parent: 'system',
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
        icon: 'i-tabler:template',
        parent: 'tools.magicManage',
      },
    },
    {
      name: 'tools.magicGroup',
      meta: {
        label: 'tools.magicGroup',
        icon: 'i-tabler:database',
        parent: 'data',
      },
    },
    {
      name: 'tools.magicSource',
      list: 'tools/magicSource',
      listElenemt: lazyComponent(() => import('../admin/magicSource/list')),
      meta: {
        label: 'tools.magicSource',
        icon: 'i-tabler:template',
        parent: 'tools.magicManage',
        sort: 0,
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
        icon: 'i-tabler:link',
        parent: 'tools',
      },
    },
  ])
}
