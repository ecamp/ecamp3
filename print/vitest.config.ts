// vitest.config.ts
import { defineConfig, configDefaults } from 'vitest/config'

export default defineConfig({
  test: {
    exclude: ['node_modules/**', 'common/**'],
    coverage: {
      all: true,
      exclude: [...(configDefaults.coverage.exclude || []), '**/.nuxt/**'],
      reporter: ['text', 'lcov', 'html'],
      reportsDirectory: './coverage',
    },
  },
})
