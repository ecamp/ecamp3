/**
 * Browserless.io REST /pdf endpoint
 * HTML generated internally and submitted to browserless as payload data
 */

import axios from 'axios'
const { loadNuxt } = require('nuxt')
const { Router } = require('express')

const router = Router()

// Test route
router.use('/test2', async (req, res) => {
  try {
    // Get nuxt instance for start (production mode)
    // Make sure to have run `nuxt build` before running this script
    const nuxt = await loadNuxt({ for: 'start' })

    // Capture HTML via internal Nuxt render call
    const { html } = await nuxt.renderRoute('/', { req }) // pass `req` object to Nuxt will also pass authentication cookies automatically

    // REST call to browserless.io
    const response = await axios({
      method: 'post',
      url: `https://chrome.browserless.io/pdf?token=${process.env.BROWSERLESS_TOKEN}`,
      responseType: 'arraybuffer',
      withCredentials: false,
      headers: {
        'Cache-Control': 'no-cache',
        Pragma: 'no-cache',
        Expires: '0',
        'Content-Type': 'application/json',
      },
      data: {
        html,
        waitFor: 1000, // this is guess-work, as we don't really know how long rendering pagedJS will take. Test4 (waiting for event) is much more robust
        options: {
          displayHeaderFooter: false,
          printBackground: true,
          format: 'A4',
          margin: {
            bottom: '0px',
            left: '0px',
            right: '0px',
            top: '0px',
          },
        },
      },
    })

    res.contentType('application/pdf')
    res.send(response.data)
  } catch (error) {
    console.log(error)
    res.send(error)
  }
})

module.exports = router
