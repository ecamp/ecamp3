import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import Components from 'unplugin-vue-components/vite'
import vuePdfStylePlugin from './vue-pdf-style-plugin.js'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue({
      template: {
        compilerOptions: {
          isCustomElement: (tag) => tag.startsWith('pdf-'),
        },
      },
    }),
    Components({ dirs: './src/renderer/components' }),
    vuePdfStylePlugin,
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
    preserveSymlinks: true,
  },
  build: {
    lib: {
      // Could also be a dictionary or array of multiple entry points
      entry: {
        pdf: './src/index.js',
        prepareInMainThread: './src/prepareInMainThread.js',
      },
      name: 'ecamp3-pdf',
      formats: ['es'],
    },
    // If we quickly delete and then recreate a source file, in Firefox the whole browser tab crashes during HMR.
    // So don't delete the output file on every build, just overwrite the old output file.
    emptyOutDir: false,
    minify: false, // for better developer experience when using the bundled script, do not minify
    sourcemap: 'hidden',
    rollupOptions: {
      // To speed up the development build greatly, we keep the fonts and other frontend-compatible
      // dependencies in the frontend. This way we can mark them as external here, which means that rollup
      // does not have to read and compile these dependencies at all, and the build can work way faster.
      // Not sure whether it is a good idea to install Vue 3 along with Vue 2 in the frontend, so Vue 3
      // is installed and compiled into the pdf module for now.
      external: (id) =>
        id.includes('assets/fonts/') ||
        id.includes('@react-pdf/') ||
        id.includes('colorjs.io') ||
        id.includes('dayjs') ||
        id.includes('lodash') ||
        id.includes('runes'),
    },
  },
  test: {
    include: ['src/**/*.{test,spec}.?(c|m)[jt]s?(x)'],
  },
})
