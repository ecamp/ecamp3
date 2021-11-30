/**
 * Browserless.io puppeteer endpoint (for more granular control)
 * HTML generated via separate call from Browserless.io to our Nuxt Print App
 */

import puppeteer from 'puppeteer'
const { Router } = require('express')

const router = Router()

// Test route
router.use('/test3', async (req, res) => {
  // Connect to browserless.io (puppeteer websocket)
  const browser = await puppeteer.connect({
    browserWSEndpoint: `wss://chrome.browserless.io/?token=${process.env.BROWSERLESS_TOKEN}`,
  })

  const page = await browser.newPage()

  try {
    // HTTP request back to Print App
    // TODO: here we need to provide some sort of authentication (pass JWT)
    await page.goto(`${process.env.PRINT_SERVER}/?pagedjs=true`, {
      waitUntil: 'networkidle0',
    })

    // print pdf
    const pdf = await page.pdf({
      displayHeaderFooter: false,
      printBackground: true,
      format: 'A4',
      margin: {
        bottom: '0px',
        left: '0px',
        right: '0px',
        top: '0px',
      },
    })
    browser.close()

    res.contentType('application/pdf')
    res.send(pdf)
  } catch (error) {
    console.error({ error }, 'Something happened!')
    browser.close()
    res.send(error)
  }
})

module.exports = router
