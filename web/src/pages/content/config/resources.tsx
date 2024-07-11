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
      name: 'content.articleExtend',
      meta: {
        parent: 'content',
        label: 'content.articleExtend',
        icon: 'i-tabler:frame',
        sort: 1,
      },
    },
    {
      name: 'content.source',
      list: 'content/source',
      listElenemt: lazyComponent(() => import('../admin/source/list')),
      meta: {
        parent: 'content.articleExtend',
        label: 'content.source',
        sort: 3,
      },
    },
    {
      name: 'content.recommend',
      list: 'content/recommend',
      create: 'content/recommend/page',
      edit: 'content/recommend/page/:id',
      listElenemt: lazyComponent(() => import('../admin/recommend/list')),
      createElenemt: lazyComponent(() => import('../admin/recommend/page')),
      editElenemt: lazyComponent(() => import('../admin/recommend/page')),
      meta: {
        parent: 'content.articleExtend',
        label: 'content.recommend',
        sort: 5,
      },
    },
    {
      name: 'content.tags',
      list: 'content/tags',
      listElenemt: lazyComponent(() => import('../admin/tags/list')),
      meta: {
        parent: 'content.articleExtend',
        label: 'content.tags',
        sort: 6,
      },
    },
    {
      name: 'content.attr',
      list: 'content/attr',
      listElenemt: lazyComponent(() => import('../admin/attr/list')),
      meta: {
        parent: 'content.articleExtend',
        label: 'content.attr',
        sort: 7,
      },
    },
    {
      name: 'content.replace',
      list: 'content/replace',
      listElenemt: lazyComponent(() => import('../admin/replace/list')),
      meta: {
        parent: 'content.articleExtend',
        label: 'content.replace',
        sort: 7,
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
    {
      name: 'content.setting',
      list: 'content/setting',
      listElenemt: lazyComponent(() => import('../admin/setting/page')),
      meta: {
        parent: 'content',
        label: 'content.setting',
        icon: 'i-tabler:settings',
        sort: 100,
      },
    },
  ])
}
