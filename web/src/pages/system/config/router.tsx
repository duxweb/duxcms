import { App } from '@duxweb/dux-refine'

import { Navigate } from 'react-router-dom'

export const adminRouter = (app: App) => {
  app.addRouter([
    {
      index: true,
      element: <Navigate to='system/index' />,
    },
  ])
}
