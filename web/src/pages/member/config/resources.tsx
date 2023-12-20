import { App, lazyComponent } from '@duxweb/dux-refine'

export const adminResources = (app: App) => {
  app.addResources([
    {
      name: 'member',
      meta: {
        label: 'member',
        sort: 80,
      },
    },
    {
      name: 'member.userGroup',
      meta: {
        label: 'member.userGroup',
        parent: 'member',
        icon: 'user',
      },
    },
    {
      name: 'member.user',
      list: 'member/user',
      listElenemt: lazyComponent(() => import('../admin/user/list')),
      meta: {
        label: 'member.user',
        parent: 'member.userGroup',
      },
    },
    {
      name: 'member.level',
      list: 'member/level',
      listElenemt: lazyComponent(() => import('../admin/level/list')),
      meta: {
        label: 'member.level',
        parent: 'member.userGroup',
      },
    },
    {
      name: 'member.setting',
      list: 'member/setting',
      listElenemt: lazyComponent(() => import('../admin/setting/page')),
      meta: {
        label: 'member.setting',
        parent: 'system',
        icon: 'user',
      },
    },
  ])
}
