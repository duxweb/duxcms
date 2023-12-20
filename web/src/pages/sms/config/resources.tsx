import { App, lazyComponent } from '@duxweb/dux-refine'

export const adminResources = (app: App) => {
  app.addResources([
    {
      name: 'sms',
      meta: {
        label: 'sms',
        icon: 'mail',
        parent: 'system',
        sort: 50,
      },
    },
    {
      name: 'sms.tpl',
      list: 'sms/tpl',
      listElenemt: lazyComponent(() => import('../admin/tpl/list')),
      meta: {
        label: 'sms.tpl',
        parent: 'sms',
      },
    },
    {
      name: 'sms.email',
      list: 'sms/email',
      listElenemt: lazyComponent(() => import('../admin/email/list')),
      meta: {
        label: 'sms.email',
        parent: 'sms',
      },
    },
    {
      name: 'sms.tpl.method',
    },
    {
      name: 'sms.setting',
      list: 'sms/setting',
      listElenemt: lazyComponent(() => import('../admin/setting/page')),
      meta: {
        label: 'sms.setting',
        parent: 'sms',
      },
    },
  ])
}
