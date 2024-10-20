import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue2'
import { createSvgPlugin } from 'vite-plugin-vue2-svg'
import { comlink } from 'vite-plugin-comlink'
import * as path from 'path'
import { VuetifyResolver } from 'unplugin-vue-components/resolvers'
import Components from 'unplugin-vue-components/vite'
import { sentryVitePlugin } from '@sentry/vite-plugin'
import { configDefaults } from 'vitest/config'

const plugins = [
  comlink(), // must be first
  vue(),
  Components({
    resolvers: [
      // Vuetify
      VuetifyResolver(),
    ],
  }),
  createSvgPlugin(),
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
  },
  plugins,
  worker: {
    plugins: () => [comlink()],
  },
  optimizeDeps: {
    include: [
      '@intlify/core',
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
      'lodash/camelCase.js',
      'lodash/cloneDeep.js',
      'lodash/groupBy.js',
      'lodash/keyBy.js',
      'lodash/sortBy.js',
      'lodash/minBy.js',
      'lodash/maxBy.js',
      'lodash/size.js',
      'runes',
      'vee-validate',
      'vue',
      'vuedraggable',
      'vue-toastification',
      'vuetify/es5/components/VCalendar/modes/column.js',
      'vuetify/es5/components/VCalendar/util/events.js',
    ],
  },
  build: {
    sourcemap: true,
    minify: mode === 'development' ? false : 'esbuild',
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
        additionalData: '@import "./node_modules/vuetify/src/styles/styles.sass";\n', // original default variables from vuetify
      },
      sass: {
        additionalData: '@import "./src/scss/variables.scss"\n', // vuetify variable overrides
      },
    },
  },
  test: {
    environment: 'jsdom',
    alias: [{ find: /^vue$/, replacement: 'vue/dist/vue.runtime.common.js' }],
    globalSetup: './tests/globalSetup.js',
    setupFiles: './tests/setup.js',
    maxWorkers: 1,
    minWorkers: 1,
    coverage: {
      all: true,
      exclude: [...configDefaults.coverage.exclude, '**/src/pdf/**'],
      reporter: ['text', 'lcov', 'html'],
      reportsDirectory: './data/coverage',
    },
    deps: {
      optimizer: {
        web: {
          exclude: ['vue'],
        },
      },
    },
  },
}))
