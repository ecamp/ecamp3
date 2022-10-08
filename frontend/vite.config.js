import { defineConfig } from 'vite'
import { createVuePlugin } from 'vite-plugin-vue2'
import { createSvgPlugin } from 'vite-plugin-vue2-svg'
import worker, { pluginHelper } from 'vite-plugin-worker'
import * as path from 'path'
import { VuetifyResolver } from 'unplugin-vue-components/resolvers'
import Components from 'unplugin-vue-components/vite'

export default defineConfig(({ mode }) => ({
  plugins: [
    createVuePlugin(),
    Components({
      resolvers: [
        // Vuetify
        VuetifyResolver(),
      ],
    }),
    createSvgPlugin(),
    pluginHelper(),
    worker({}),
  ],
  optimizeDeps: {
    include: [
      'core-js/modules/es.symbol.js',
      'core-js/modules/es.symbol.description.js',
      'core-js/modules/es.function.name.js',
      'core-js/modules/es.array.concat.js',
      'core-js/modules/es.array.splice.js',
      'core-js/modules/es.array.find.js',
      'core-js/modules/es.array.push.js',
      'core-js/modules/es.object.to-string.js',
      '@sentry/browser',
      'raf/polyfill',
      'vuetify/es5/components/VCalendar/modes/column.js',
      'vuetify/es5/components/VCalendar/util/events.js',
      'lodash/keyBy.js',
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
