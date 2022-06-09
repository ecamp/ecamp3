const { defineConfig } = require('cypress')

module.exports = defineConfig({
  video: false,
  pageLoadTimeout: 120000,
  defaultCommandTimeout: 8000,
  fixturesFolder: 'tests/e2e/fixtures',
  screenshotsFolder: 'data/e2e/screenshots',
  videosFolder: 'data/e2e/videos',
  e2e: {
    setupNodeEvents (on, config) {
      return config
    },
    specPattern: 'tests/e2e/specs/**/*.cy.{js,jsx,ts,tsx}',
    supportFile: 'tests/e2e/support/index.js'
  }
})
