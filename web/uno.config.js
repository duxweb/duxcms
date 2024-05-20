import { defineConfig, presetTypography, presetUno, transformerDirectives } from 'unocss'
import presetIcons from '@unocss/preset-icons/browser'
import { presetDux } from '@duxweb/dux-plugin'
import tablerIcon from '@iconify-json/tabler/icons.json'

const iconSafeList = []
Object.keys(tablerIcon.icons).map((item) => iconSafeList.push(`i-tabler:${item}`))

export default defineConfig({
  presets: [
    presetUno({
      dark: {
        dark: '[theme-mode="dark"]',
      },
    }),
    presetTypography(),
    presetDux(),
    presetIcons({
      prefix: 'i-',
      collections: {
        tabler: () => import('@iconify-json/tabler/icons.json').then((i) => i.default),
      },
    }),
  ],
  safelist: iconSafeList,
  transformers: [transformerDirectives()],
  content: {
    pipeline: {
      include: [
        // the default
        /\.(vue|svelte|[jt]sx|mdx?|astro|elm|php|phtml|html)($|\?)/,
        // include js/ts files
        'src/**/*.{js,ts,jsx,tsx}',
        // dux refine
        /dux-refine\.(js|jsx|tsx|ts|css)$/,
        /dux-extend\.(js|jsx|tsx|ts|css)$/,
        './node_modules/@duxweb/dux-refine/**/*.{js,ts,jsx,tsx,css}',
        './node_modules/@duxweb/dux-extend/**/*.{js,ts,jsx,tsx,css}',
      ],
      // exclude files
      exclude: [],
    },
  },

  shortcuts: {},
  screens: {},
})
