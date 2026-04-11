// vitest.config.ts
import { configDefaults } from 'vitest/config'
import { defineVitestConfig } from '@nuxt/test-utils/config'

export default defineVitestConfig({
  test: {
    environment: 'nuxt',
    exclude: ['node_modules/**', 'common/**'],
    coverage: {
      all: true,
      exclude: [...(configDefaults.coverage.exclude || []), '**/.nuxt/**', 'test/**'],
      reporter: ['text', 'lcov', 'html'],
      reportsDirectory: './coverage',
    },
  },
})
