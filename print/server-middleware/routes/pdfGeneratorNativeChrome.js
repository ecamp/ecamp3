/**
 * Browserless.io puppeteer endpoint (for more granular control)
 * HTML generated internally and loaded into puppeteer
 *
 * For running locally (puppeteer.launch), the following packages are needed on top of standard WSL2-Ubuntu:
 * sudo apt-get install libnss3-dev libxkbcommon0 libgbm1
 * sudo apt-get install -y gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget libgbm-dev
 */

import puppeteer from 'puppeteer-core'
const { performance } = require('perf_hooks')
const { URL } = require('url')
const { Router } = require('express')
const { memoryUsage } = require('process')

const router = Router()

let lastTime = null
function measurePerformance(msg) {
  const now = performance.now()
  const memory = memoryUsage()

  if (lastTime !== null) {
    console.log(
      `(took ${Math.round(now - lastTime)} millisecons / using ${
        memory.heapUsed / 1000000
      }MB)`
    )
  }
  lastTime = now

  console.log('\n')
  console.log(msg || '')
}

// Test route
router.use('/pdfChrome', async (req, res) => {
  let browser = null

  try {
    measurePerformance('Connecting to puppeteer...')
    // Connect to browserless.io (puppeteer websocket)
    browser = await puppeteer.connect({
      browserWSEndpoint: process.env.BROWSER_WS_ENDPOINT,
    })
    const context = await browser.createIncognitoBrowserContext()

    measurePerformance('Open new page & set cookies...')
    const page = await context.newPage()
    const printUrl = new URL(process.env.PRINT_URL)
    const cookies = [
      {
        name: `${process.env.COOKIE_PREFIX}jwt_s`,
        value: req.cookies[`${process.env.COOKIE_PREFIX}jwt_s`],
        domain: printUrl.host,
        httpOnly: true,
        path: '/',
        sameSite: 'Lax',
        secure: false,
      },
      {
        name: `${process.env.COOKIE_PREFIX}jwt_hp`,
        value: req.cookies[`${process.env.COOKIE_PREFIX}jwt_hp`],
        domain: printUrl.host,
        httpOnly: true,
        path: '/',
        sameSite: 'Lax',
        secure: false,
      },
    ]
    await page.setCookie(...cookies)

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
    measurePerformance('Puppeteer load HTML content...')
    page.setUserAgent(
      'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:96.0) Gecko/20100101 Firefox/96.0'
    )

    // HTTP request back to Print Nuxt App
    await page.goto(`${process.env.PRINT_URL}/?config=${req.query.config}`, {
      waitUntil: 'networkidle0',
    })

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
    browser.disconnect()

    res.contentType('application/pdf')
    res.send(pdf)
  } catch (error) {
    if (browser) {
      browser.disconnect()
    }

    let errorMessage = null
    if (error.error) {
      // error is a WebScocket ErrorEvent Object which contains an error property
      errorMessage = error.error.toString()
      res.status(503)
    } else {
      errorMessage = error.toString()
      res.status(500)
    }

    console.error(error)

    res.contentType('application/json')
    res.send({ error: errorMessage })
  }
})

module.exports = router
