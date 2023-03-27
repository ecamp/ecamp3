const { defineConfig } = require('cypress')
const { deleteDownloads } = require('./tasks/deleteDownloads')

module.exports = defineConfig({
  video: false,
  pageLoadTimeout: 120000,
  defaultCommandTimeout: 8000,
  screenshotsFolder: 'data/screenshots',
  videosFolder: 'data/videos',
  downloadsFolder: 'data/downloads',
  trashAssetsBeforeRuns: false,
  e2e: {
    setupNodeEvents(on, config) {
      on('task', {
        deleteDownloads: () => deleteDownloads(config),
      })
    },
    specPattern: 'specs/**/*.cy.{js,jsx,ts,tsx}',
    supportFile: 'support/index.js',
    baseUrl: 'http://localhost:3000',
  },
  env: {
    PRINT_URL: 'http://localhost:3000/print',
    API_ROOT_URL: 'http://localhost:3000/api',
  },
})
