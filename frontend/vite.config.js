import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue2'
import { createSvgPlugin } from 'vite-plugin-vue2-svg'
import { comlink } from 'vite-plugin-comlink'
import * as path from 'path'
import { VuetifyResolver } from 'unplugin-vue-components/resolvers'
import Components from 'unplugin-vue-components/vite'

export default defineConfig(({ mode }) => ({
  server: {
    port: 3000,
  },
  plugins: [
    comlink(), // must be first
    vue(),
    Components({
      resolvers: [
        // Vuetify
        VuetifyResolver(),
      ],
    }),
    createSvgPlugin(),
  ],
  worker: {
    plugins: [comlink()],
  },
  optimizeDeps: {
    include: [
      '@sentry/browser',
      '@sentry/vue',
      '@zxcvbn-ts/core',
      '@zxcvbn-ts/language-common',
      '@zxcvbn-ts/language-de',
      '@zxcvbn-ts/language-en',
      '@zxcvbn-ts/language-fr',
      '@zxcvbn-ts/language-it',
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
      'lodash/keyBy.js',
      'raf/polyfill',
      'vee-validate',
      'vue',
      'vue-toastification',
      'vuetify/es5/components/VCalendar/modes/column.js',
      'vuetify/es5/components/VCalendar/util/events.js',
    ],
  },
  build: {
    sourcemap: mode === 'development',
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
        additionalData: [
          '@import "./node_modules/vuetify/src/styles/styles.sass";', // original default variables from vuetify
          '@import "@/scss/variables.scss";', // vuetify variable overrides
          '',
        ].join('\n'),
      },
      sass: {
        additionalData: [
          '@import "./node_modules/vuetify/src/styles/styles.sass"', // original default variables from vuetify
          '@import "@/scss/variables.scss"', // vuetify variable overrides
          '',
        ].join('\n'),
      },
    },
  },
}))
