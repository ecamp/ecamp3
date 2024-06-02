/**
 * Browserless.io puppeteer endpoint (for more granular control)
 * HTML generated internally and loaded into puppeteer
 *
 * For running locally (puppeteer.launch), the following packages are needed on top of standard WSL2-Ubuntu:
 * sudo apt-get install libnss3-dev libxkbcommon0 libgbm1
 * sudo apt-get install -y gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget libgbm-dev
 */

import puppeteer from 'puppeteer-core'
import { performance } from 'perf_hooks'
import { URL } from 'url'
import { memoryUsage } from 'process'
import * as Sentry from '@sentry/node'

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

export default defineEventHandler(async (event) => {
  const {
    basicAuthToken,
    browserWsEndpoint,
    printUrl,
    cookiePrefix,
    renderHtmlTimeoutMs,
    renderPdfTimeoutMs,
  } = useRuntimeConfig(event)

  let browser = null

  try {
    measurePerformance('Connecting to puppeteer...')
    // Connect to browserless.io (puppeteer websocket)
    browser = await puppeteer.connect({
      browserWSEndpoint: browserWsEndpoint,
    })
    const context = await browser.createIncognitoBrowserContext()

    measurePerformance('Open new page & set cookies...')
    const page = await context.newPage()
    const printUrlObj = new URL(printUrl)
    const requestCookies = parseCookies(event)
    const cookies = [
      {
        name: `${cookiePrefix}jwt_s`,
        value: requestCookies[`${cookiePrefix}jwt_s`],
        domain: printUrlObj.host,
        httpOnly: true,
        path: '/',
        sameSite: 'Lax',
        secure: false,
      },
      {
        name: `${cookiePrefix}jwt_hp`,
        value: requestCookies[`${cookiePrefix}jwt_hp`],
        domain: printUrlObj.host,
        httpOnly: true,
        path: '/',
        sameSite: 'Lax',
        secure: false,
      },
    ]
    await page.setCookie(...cookies)

    const extraHeaders = {}
    if (basicAuthToken) {
      extraHeaders['Authorization'] = `Basic ${basicAuthToken}`
      await page.setExtraHTTPHeaders(extraHeaders)
    }

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
    const queryParams = getQuery(event)
    await page.goto(`${printUrl}/?config=${queryParams.config}`, {
      timeout: renderHtmlTimeoutMs || 30000,
      waitUntil: 'networkidle0',
    })

    // print pdf
    measurePerformance('Generate PDf...')
    const pdf = await page.pdf({
      printBackground: true,
      format: 'A4',
      scale: 1,
      displayHeaderFooter: true,
      headerTemplate: `<div id="header-template" style="font-size:7pt; text-align: center; width: 100%; font-family: Helvetica, sans-serif; font-weight: 500"><span>eCamp v3</span></div>`,
      footerTemplate: `<div id="footer-template" style="font-size:7pt; text-align: center; width: 100%; font-family: Helvetica, sans-serif; font-weight: 500"><span class="pageNumber"></span> / <span class="totalPages"></span></div>`,
      margin: {
        bottom: '15mm',
        left: '15mm',
        right: '15mm',
        top: '15mm',
      },
      timeout: renderPdfTimeoutMs || 30000,
    })

    measurePerformance()
    browser.disconnect()

    defaultContentType(event, 'application/pdf')
    return pdf
  } catch (error) {
    if (browser) {
      browser.disconnect()
    }

    let errorMessage = null
    let status = 500
    if (error.error) {
      // error is a WebSocket ErrorEvent Object which contains an error property
      errorMessage = error.error.message
      if (errorMessage === 'Unexpected server response: 429') {
        status = 503
        errorMessage = 'Server responded with `429 Too Many Requests` (queue is full)'
      }
    } else {
      errorMessage = error.message
    }

    captureError(error)

    setResponseStatus(event, status)
    defaultContentType(event, 'application/problem+json')
    return { status, title: errorMessage }
  }
})

/**
 * @param {Error} error
 */
function captureError(error) {
  if (Sentry.isInitialized()) {
    Sentry.captureException(error)
  } else {
    console.error(error)
  }
}
