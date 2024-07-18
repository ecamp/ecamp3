// ***********************************************************
// This example support/index.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import './commands'

function apiAvailable(retries = 0) {
  if (retries > 10) {
    throw new Error('Request failed')
  }

  cy.log('executing request')

  cy.request('OPTIONS', Cypress.env('API_ROOT_URL'), {
    headers: {
      Origin: 'http://localhost:3000',
      'Access-Control-Request-Method': 'GET',
      'Access-Control-Request-Headers': 'Origin, Content-Type, Accept, Authorization',
    },
  }).then((resp) => {
    if (resp.status === 200) {
      return
    }
    // eslint-disable-next-line cypress/no-unnecessary-waiting
    cy.wait(10000).then(() => apiAvailable())
  })
}

beforeEach(() => {
  apiAvailable()
})

// Alternatively you can use CommonJS syntax:
// require('./commands')
