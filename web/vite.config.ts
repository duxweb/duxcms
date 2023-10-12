import react from '@vitejs/plugin-react'
import * as path from 'path'
import UnoCSS from 'unocss/vite'
import { defineConfig } from 'vite'
import { DuxUI, DuxTheme } from '@duxweb/dux-plugin'

export default defineConfig({
  plugins: [react(), UnoCSS(), DuxUI()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src'),
    },
  },
  css: {
    preprocessorOptions: {
      less: {
        modifyVars: DuxTheme(),
      },
    },
  },
  base: '/web/',
  server: {
    origin: 'http://127.0.0.1:5173',
  },
  build: {
    cssCodeSplit: false,
    outDir: '../public/web/',
    manifest: true,
    rollupOptions: {
      input: {
        index: '/src/index.tsx',
        install: '/src/install.tsx',
      },
    },
  },
})
