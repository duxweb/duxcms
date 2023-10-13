import { App, lazyComponent } from '@duxweb/dux-refine'

import { Navigate } from 'react-router-dom'

export const adminRouter = (app: App) => {
  app.addRouter([
    {
      index: true,
      element: <Navigate to='system/index' />,
    },
    {
      path: 'system/role',
      element: lazyComponent(() => import('../admin/role/list')),
    },
    {
      path: 'system/user',
      element: lazyComponent(() => import('../admin/user/list')),
    },
    {
      path: 'system/operate',
      element: lazyComponent(() => import('../admin/operate/list')),
    },
    {
      path: 'system/api',
      element: lazyComponent(() => import('../admin/api/list')),
    },
  ])
}
