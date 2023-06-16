import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import Components from 'unplugin-vue-components/vite'

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
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  build: {
    lib: {
      // Could also be a dictionary or array of multiple entry points
      entry: './src/index.js',
      name: 'ecamp3-pdf',
    },
    minify: false, // for better developer experience when using the bundled script, do not minify
    rollupOptions: {
      external: (id) => id.startsWith('@react-pdf/'),
    },
  },
})
