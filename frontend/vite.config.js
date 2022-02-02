import { defineConfig } from 'vite'
import { createVuePlugin } from 'vite-plugin-vue2'
import { createSvgPlugin } from 'vite-plugin-vue2-svg'
import ViteComponents, { VuetifyResolver } from 'vite-plugin-components'
import * as path from 'path'

export default defineConfig(({ mode }) => ({
  plugins: [
    createVuePlugin(),
    ViteComponents({
      customComponentResolvers: [VuetifyResolver()]
    }),
    createSvgPlugin()
  ],
  build: {
    sourcemap: mode === 'development',
    minify: mode === 'development' ? false : 'esbuild'
  },
  resolve: {
    alias: [
      // webpack alias @ (import in Vue files)
      {
        find: '~@',
        replacement: path.resolve(__dirname, 'src')
      },
      {
        find: '@',
        replacement: path.resolve(__dirname, 'src')
      },

      // individual webpack aliases for ~ (node modules)
      {
        find: '~@mdi',
        replacement: path.resolve(__dirname, 'node_modules', '@mdi')
      },
      {
        find: '~inter-ui',
        replacement: path.resolve(__dirname, 'node_modules', 'inter-ui')
      },

      // find dayjs from commons
      {
        find: 'dayjs',
        replacement: path.resolve(__dirname, 'node_modules', 'dayjs')
      }
    ],
    preserveSymlinks: true
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: [
          '@import "./node_modules/vuetify/src/styles/styles.sass";', // original default variables from vuetify
          '@import "@/scss/variables.scss";', // vuetify variable overrides
          ''
        ].join('\n')
      },
      sass: {
        additionalData: [
          '@import "./node_modules/vuetify/src/styles/styles.sass"', // original default variables from vuetify
          '@import "@/scss/variables.scss"', // vuetify variable overrides
          ''
        ].join('\n')
      }
    }
  }
}))
