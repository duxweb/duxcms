import { createRoot } from 'react-dom/client'
import { AppProvider, DuxApp } from '@duxweb/dux-refine'

import '@unocss/reset/tailwind-compat.css'
import 'virtual:uno.css'

import app from './config/app'
import config from './config'

import 'vite/modulepreload-polyfill'

const container = document.getElementById('root') as HTMLElement
const root = createRoot(container)

root.render(
  <DuxApp config={config}>
    <AppProvider appsData={app} config={config} />
  </DuxApp>,
)
