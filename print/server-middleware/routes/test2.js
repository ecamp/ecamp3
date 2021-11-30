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
    // TODO: needs some extra work to ensure pagedJs or all other dependencies are already included in generated HTML (not just as links). Browserless.io will not try to fetch additionals links.
    // TODO: check if authentication needs to be providfed (or if Nuxt is already aware)
    const { html } = await nuxt.renderRoute('/')

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
