import { defineConfig, devices } from '@playwright/test'

// noinspection JSUnusedGlobalSymbols
const multipleBrowserTests = ['0-snapshot-tests/**/*', '5-cross-browser-tests/**/*']
export default defineConfig({
  testDir: './tests',
  timeout: 120000,
  expect: {
    timeout: 8000,
  },
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: 0,
  workers: undefined,
  reporter: process.env.CI ? 'blob' : [['html', { open: 'never' }]],
  use: {
    baseURL: 'http://localhost:3000',
    trace: 'retain-on-failure',
    video: 'off',
    screenshot: 'only-on-failure',
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
      testMatch: multipleBrowserTests,
    },
    {
      name: 'firefox',
      use: { ...devices['Desktop Firefox'] },
      testMatch: multipleBrowserTests,
    },
    {
      name: 'webkit',
      use: { ...devices['Desktop Safari'] },
      testMatch: multipleBrowserTests,
    },
    {
      name: 'behavior-tests',
      use: { ...devices['Desktop Chrome'] },
      testMatch: ['9-behavior-tests/**/*'],
      fullyParallel: false,
      workers: 1,
    },
  ],
})
