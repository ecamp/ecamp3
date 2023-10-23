// vitest.config.ts
import { defineConfig } from 'vitest/config'

export default defineConfig({
    test: {
        coverage: {
            all: true,
            reporter: ['text', 'lcov', 'html'],
            reportsDirectory: './coverage',
        },
    },
})