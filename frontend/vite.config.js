// vite.config.js

import { defineConfig } from 'vite'
import { createVuePlugin } from 'vite-plugin-vue2'
import { createSvgPlugin } from 'vite-plugin-vue2-svg'
import ViteComponents, { VuetifyResolver } from 'vite-plugin-components'
import requireContext from 'rollup-plugin-require-context'

import * as path from 'path'

export default defineConfig({
  plugins: [
    createVuePlugin(),
    ViteComponents({
      customComponentResolvers: [VuetifyResolver()]
    }),
    createSvgPlugin()
  ],
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
      }
    ]
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: '@import "./node_modules/vuetify/src/styles/styles.sass";' // pre-insert vuetify sass variables for usage of variables in components
      }
    }
  },
  rollupInputOptions: {
    plugins: [
      requireContext() // polyfill for webpack require.context
    ]
  }
})
