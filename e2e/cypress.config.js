const { defineConfig } = require('cypress')

const fs = require('fs')
const path = require('path')

module.exports = defineConfig({
  video: false,
  pageLoadTimeout: 120000,
  defaultCommandTimeout: 8000,
  screenshotsFolder: 'data/screenshots',
  videosFolder: 'data/videos',
  downloadsFolder: 'data/downloads',
  e2e: {
    setupNodeEvents(on, config) {
      on('task', {
        deleteDownloads() {
          const dirPath = config.downloadsFolder
          fs.readdir(dirPath, (err, files) => {
            if (err) {
              console.log(err)
            } else {
              files.forEach((file) => {
                fs.unlink(path.join(dirPath, file), () => {
                  console.log('Removed ' + file)
                })
              })
            }
          })
          return null
        },
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
