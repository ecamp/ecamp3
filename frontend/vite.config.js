import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { comlink } from 'vite-plugin-comlink'
import * as path from 'path'
import Components from 'unplugin-vue-components/vite'
import { sentryVitePlugin } from '@sentry/vite-plugin'
import { configDefaults } from 'vitest/config'
import svgLoader from 'vite-svg-loader'
import Vuetify from 'vite-plugin-vuetify'
import { readdirSync } from 'fs'

const componentsPath = 'node_modules/vuetify/lib/components'
const vuetifyComponents = readdirSync(componentsPath)
  .filter((file) => file.startsWith('V'))
  .map((file) => `vuetify/components/${file}`)

const plugins = [
  comlink(), // must be first
  vue(),
  Components({
    resolvers: [],
  }),
  svgLoader(),
  Vuetify({
    autoImport: {
      labs: true,
    },
    styles: { configFile: 'src/scss/settings.scss' },
  }),
]
const sentryAuthToken = process.env.SENTRY_AUTH_TOKEN
if (sentryAuthToken) {
  plugins.push(
    sentryVitePlugin({
      authToken: sentryAuthToken,
      org: process.env.SENTRY_ORG || 'ecamp',
      project: process.env.SENTRY_FRONTEND_PROJECT || 'ecamp3-frontend',
      telemetry: false,
      sourcemaps: {
        assets: ['./dist/**/*'],
      },
      release: {
        name: process.env.SENTRY_RELEASE_NAME || 'development',
      },
    })
  )
}

export default defineConfig(({ mode }) => ({
  server: {
    port: 3000,
    allowedHosts: ['frontend', 'localhost:3000'],
  },
  plugins,
  worker: {
    plugins: () => [comlink()],
  },
  optimizeDeps: {
    include: [
      ...vuetifyComponents.filter((dep) => !dep.includes('VOverflowBtn')),
      '@intlify/core',
      '@leeoniya/ufuzzy',
      '@react-pdf/font',
      '@react-pdf/layout',
      '@react-pdf/pdfkit',
      '@react-pdf/primitives',
      '@react-pdf/render',
      '@sentry/browser',
      '@sentry/vue',
      '@tiptap/pm/state',
      '@tiptap/pm/view',
      '@zxcvbn-ts/core',
      '@zxcvbn-ts/language-common',
      '@zxcvbn-ts/language-de',
      '@zxcvbn-ts/language-en',
      '@zxcvbn-ts/language-fr',
      '@zxcvbn-ts/language-it',
      'colorjs.io',
      'comlink',
      'core-js/modules/es.array.concat.js',
      'core-js/modules/es.array.find.js',
      'core-js/modules/es.array.push.js',
      'core-js/modules/es.array.slice.js',
      'core-js/modules/es.array.splice.js',
      'core-js/modules/es.function.name.js',
      'core-js/modules/es.object.to-string.js',
      'core-js/modules/es.regexp.exec.js',
      'core-js/modules/es.regexp.test.js',
      'core-js/modules/es.symbol.description.js',
      'core-js/modules/es.symbol.js',
      'dayjs',
      'dayjs/locale/de',
      'dayjs/locale/de-ch',
      'dayjs/locale/fr',
      'dayjs/locale/it',
      'dayjs/plugin/customParseFormat',
      'dayjs/plugin/isBetween',
      'dayjs/plugin/isSameOrBefore',
      'dayjs/plugin/isSameOrAfter',
      'dayjs/plugin/localizedFormat',
      'dayjs/plugin/timezone',
      'dayjs/plugin/utc',
      'file-saver',
      'linkify-it',
      'runes',
      'vee-validate',
      'vite-plugin-comlink/symbol',
      'vue',
      'vuedraggable',
      'vue-toastification',
      // 'vuetify/es5/components/VCalendar/modes/column.js',
      // 'vuetify/es5/components/VCalendar/util/events.js',
    ],
    exclude: ['vue-i18n'],
  },
  build: {
    sourcemap: true,
    minify: mode === 'development' ? false : 'esbuild',
    rollupOptions: {
      external: ['vuetify/lib'],
    },
  },
  resolve: {
    alias: [
      // webpack alias @ (import in Vue files)
      {
        find: '~@',
        replacement: path.resolve(__dirname, 'src'),
      },
      {
        find: '@',
        replacement: path.resolve(__dirname, 'src'),
      },

      // individual webpack aliases for ~ (node modules)
      {
        find: '~@mdi',
        replacement: path.resolve(__dirname, 'node_modules', '@mdi'),
      },
      {
        find: '~inter-ui',
        replacement: path.resolve(__dirname, 'node_modules', 'inter-ui'),
      },

      // find dayjs from commons
      {
        find: 'dayjs',
        replacement: path.resolve(__dirname, 'node_modules', 'dayjs'),
      },
    ],
    preserveSymlinks: true,
  },
  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler',
      },
      sass: {
        api: 'modern-compiler',
      },
    },
  },
  test: {
    environment: 'jsdom',
    globalSetup: './tests/globalSetup.js',
    setupFiles: './tests/setup.js',
    maxWorkers: 1,
    minWorkers: 1,
    coverage: {
      include: ['src/**/*'],
      exclude: [...configDefaults.coverage.exclude, '**/src/pdf/**'],
      reporter: ['text', 'lcov', 'html'],
      reportsDirectory: './data/coverage',
    },
    server: {
      deps: {
        inline: ['vuetify'],
      },
    },
  },
}))
