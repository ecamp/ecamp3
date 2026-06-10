// vitest.config.ts
import { configDefaults } from 'vitest/config'
import { defineVitestConfig } from '@nuxt/test-utils/config'

export default defineVitestConfig({
  test: {
    environment: 'nuxt',
    exclude: ['node_modules/**', 'common/**'],
    coverage: {
      include: ['test/**/*'],
      exclude: [...(configDefaults.coverage.exclude || []), '**/.nuxt/**', 'test/**'],
      reporter: ['text', 'lcov', 'html'],
      reportsDirectory: './coverage',
    },
  },
})
