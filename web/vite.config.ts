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
    origin: 'http://localhost:5173',
    // proxy: {
    //   '/admin': 'https://localhost',
    // },
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
      output: {
        manualChunks: {
          'vendor-react': ['react', 'react-dom'],
          'vendor-tdesign': ['tdesign-react/esm'],
          'vendor-refine': ['@refinedev/core'],
          'vendor-echarts': ['echarts', 'echarts-for-react'],
          'vendor-lib': ['prismjs', 'ace-builds', 'react-ace', 'dayjs'],
          'vendor-map': ['@uiw/react-baidu-map'],
          'vendor-markdown': ['md-editor-rt', 'mermaid', 'highlight.js', 'prettier'],
          'vendor-tinymce': [
            'tinymce',
            'tinymce/themes/silver',
            'tinymce/icons/default',
            'tinymce/models/dom/model',
          ],
        },
      },
    },
  },
})
