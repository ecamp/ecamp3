/**
 * Browserless.io REST /pdf endpoint
 * HTML generated via separate call from Browserless.io to our Nuxt Print App
 */

import axios from 'axios'
const { Router } = require('express')

const router = Router()

// Test route
router.use('/test1', (req, res) => {
  // REST call to browserless.io
  axios({
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
      url: `${process.env.PRINT_SERVER}/?pagedjs=true`, // HTTP request back to Print App
      gotoOptions: {
        waitUntil: 'networkidle0',
        // TODO: here we need to provide some sort of authentication (pass JWT)
      },
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
      // pass JWT cookie
      /*
      cookies: [
        {
          name: 'jwt_hp',
          value: req.cookies.jwt_hp,
        },
        {
          name: 'jwt_s',
          value: req.cookies.jwt_s,
        },
      ], */
    },
  })
    .then((response) => {
      res.contentType('application/pdf')
      res.send(response.data)
    })
    .catch((error) => {
      console.log(error)
    })
})

module.exports = router
