/**
 * Browserless.io puppeteer endpoint (for more granular control)
 * HTML generated internally and loaded into puppeteer
 *
 * For running locally (puppeteer.launch), the following packages are needed on top of standard WSL2-Ubuntu:
 * sudo apt-get install libnss3-dev libxkbcommon0 libgbm1
 * sudo apt-get install -y gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget libgbm-dev
 */

import puppeteer from 'puppeteer'
const { performance } = require('perf_hooks')
const { URL } = require('url')
const { loadNuxt, build } = require('nuxt')
const { Router } = require('express')

const router = Router()

let lastTime = null
function measurePerformance(msg) {
  const now = performance.now()
  if (lastTime !== null) {
    console.log(`(took ${Math.round(now - lastTime)} millisecons)`)
  }
  lastTime = now

  console.log('\n')
  console.log(msg)
}

// Test route
router.use('/pdfChrome', async (req, res) => {
  let browser = null

  try {
    measurePerformance('Building Nuxt if necessary... ' + process.env.NODE_ENV)

    const isDev = process.env.NODE_ENV !== 'production'
    // Get nuxt instance for start (production mode)
    const nuxt = await loadNuxt(isDev ? 'dev' : 'start')

    // Enable live build & reloading on dev
    if (isDev) {
      await build(nuxt)
    }

    // Capture HTML via internal Nuxt render call
    measurePerformance('Rendering page in Nuxt...')
    const url = new URL(req.url, `http://${req.headers.host}`)
    const queryString = url.search
    const { html } = await nuxt.renderRoute('/' + queryString, { req }) // pass `req` object to Nuxt will also pass authentication cookies automatically

    measurePerformance('Connecting to puppeteer...')

    // Connect to browserless.io (puppeteer websocket)
    browser = await puppeteer.connect({
      browserWSEndpoint: process.env.BROWSER_WS_ENDPOINT,
    })

    const page = await browser.newPage()

    /**
     * Debugging puppeteer
     */
    /*
    page.on('request', (request) =>
      console.log('>>', request.method(), request.url())
    )
    page.on('response', (response) =>
      console.log('<<', response.status(), response.url())
    ) 
    page.on('error', (err) => {
      console.log('error happen at the page: ', err)
    })
    page.on('pageerror', (pageerr) => {
      console.log('pageerror occurred: ', pageerr)
    }) */

    // set HTML content of current page
    measurePerformance('Puppeteer set HTML content & load resources...')
    page.setUserAgent(
      'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:96.0) Gecko/20100101 Firefox/96.0'
    )
    await page.setContent(html, { waitUntil: 'networkidle0' })

    // print pdf
    measurePerformance('Generate PDf...')
    const pdf = await page.pdf({
      printBackground: true,
      format: 'A4',
      displayHeaderFooter: true,
      headerTemplate: `<div id="header-template" style="font-size:8pt; text-align: center; width: 100%; font-family: "Open Sans", sans-serif;">eCamp3</span></div>`,
      footerTemplate: `<div id="footer-template" style="font-size:8pt; text-align: center; width: 100%; font-family: "Open Sans", sans-serif;"><span class="pageNumber"></span> / <span class="totalPages"></span></div>`,
      margin: {
        bottom: '20mm',
        left: '15mm',
        right: '15mm',
        top: '20mm',
      },
    })

    measurePerformance()
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
