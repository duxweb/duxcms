import { App, lazyComponent } from '@duxweb/dux-refine'

export const adminResources = (app: App) => {
  app.addResources([
    {
      name: 'content',
      meta: {
        label: 'content',
        sort: 20,
        icon: 'i-tabler:article',
      },
    },
    {
      name: 'content.articleGroup',
      meta: {
        parent: 'content',
        label: 'content.articleGroup',
        icon: 'i-tabler:book',
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
        sort: 1,
      },
    },
    {
      name: 'content.category',
      list: 'content/category',
      listElenemt: lazyComponent(() => import('../admin/category/list')),
      meta: {
        parent: 'content.articleGroup',
        label: 'content.category',
        sort: 2,
      },
    },
    {
      name: 'content.source',
      list: 'content/source',
      listElenemt: lazyComponent(() => import('../admin/source/list')),
      meta: {
        parent: 'content.articleGroup',
        label: 'content.source',
        sort: 3,
      },
    },
    {
      name: 'content.recommend',
      list: 'content/recommend',
      listElenemt: lazyComponent(() => import('../admin/recommend/list')),
      meta: {
        parent: 'content.articleGroup',
        label: 'content.recommend',
        sort: 5,
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
        icon: 'i-tabler:flag',
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
        icon: 'i-tabler:menu-2',
        sort: 10,
      },
    },
    {
      name: 'content.menu',
    },
  ])
}
