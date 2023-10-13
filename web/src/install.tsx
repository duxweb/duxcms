import { createRoot } from 'react-dom/client'
import { DuxApp, DuxLogo } from '@duxweb/dux-refine'
import { InstallApp } from '@duxweb/dux-extend'

import '@unocss/reset/tailwind-compat.css'
import 'virtual:uno.css'

import config from './config'

import 'vite/modulepreload-polyfill'

const container = document.getElementById('root') as HTMLElement
const root = createRoot(container)

root.render(
  <DuxApp config={config}>
    <InstallApp
      config={config}
      pathDetection='/install/detection'
      pathConfig='/install/config'
      pathComplete='/install/complete'
      logo={<DuxLogo className='h-10' />}
    />
  </DuxApp>
)
