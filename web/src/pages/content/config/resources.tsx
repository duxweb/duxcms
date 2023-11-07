import { App, lazyComponent } from '@duxweb/dux-refine'

export const adminResources = (app: App) => {
  app.addResources([
    {
      name: 'content',
      meta: {
        label: 'content',
        sort: 20,
      },
    },
    {
      name: 'content.articleGroup',
      meta: {
        parent: 'content',
        label: 'content.articleGroup',
        icon: 'book',
        sort: 1,
      },
    },
    {
      name: 'content.article',
      list: 'article/article',
      create: 'content/article/page',
      edit: 'content/article/page/:id',
      listElenemt: lazyComponent(() => import('../admin/article/list')),
      createElenemt: lazyComponent(() => import('../admin/article/page')),
      editElenemt: lazyComponent(() => import('../admin/article/page')),
      meta: {
        parent: 'content.articleGroup',
        label: 'content.article',
      },
    },
    {
      name: 'content.category',
      list: 'content/category',
      listElenemt: lazyComponent(() => import('../admin/category/list')),
      meta: {
        parent: 'content.articleGroup',
        label: 'content.category',
      },
    },

    {
      name: 'content.source',
      list: 'content/source',
      listElenemt: lazyComponent(() => import('../admin/source/list')),
      meta: {
        parent: 'content.articleGroup',
        label: 'content.source',
      },
    },
    {
      name: 'content.page',
      list: 'content/page',
      create: 'content/page/page',
      edit: 'content/page/page/:id',
      listElenemt: lazyComponent(() => import('../admin/page/list')),
      createElenemt: lazyComponent(() => import('../admin/page/page')),
      editElenemt: lazyComponent(() => import('../admin/page/page')),
      meta: {
        parent: 'content',
        label: 'content.page',
        icon: 'file-1',
        sort: 2,
      },
    },
    {
      name: 'content.menuData',
      list: 'content/menu',
      listElenemt: lazyComponent(() => import('../admin/menu/list')),
      meta: {
        parent: 'content',
        label: 'content.menu',
        icon: 'collection',
        sort: 10,
      },
    },
    {
      name: 'content.menu',
    },
  ])
}
