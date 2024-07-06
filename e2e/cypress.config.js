const { defineConfig } = require('cypress')
const { moveDownloads } = require('./tasks/moveDownloads')
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
        moveDownloads: (destSubDir) => moveDownloads(config, destSubDir),
      })
    },
    specPattern: 'specs/**/*.cy.{js,jsx,ts,tsx}',
    supportFile: 'support/index.js',
    baseUrl: 'http://localhost:3000',
    retries: {
      runMode:
        'E2E_RUNMODE_RETRIES' in process.env
          ? parseInt(process.env.E2E_RUNMODE_RETRIES)
          : 0,
      openMode: 0,
    },
  },
  env: {
    PRINT_URL: 'http://localhost:3000/print',
    API_ROOT_URL: 'http://localhost:3000/api',
    API_ROOT_URL_CACHED: 'http://localhost:3004',
  },
})
