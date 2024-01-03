// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add("login", (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add("drag", { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add("dismiss", { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This is will overwrite an existing command --
// Cypress.Commands.overwrite("visit", (originalFn, url, options) => { ... })

Cypress.Commands.add('login', (identifier) => {
  cy.session(identifier, () => {
    cy.request({
      method: 'POST',
      url: Cypress.env('API_ROOT_URL') + '/authentication_token',
      body: { identifier, password: 'test' },
    })
  })
})

Cypress.Commands.add('moveDownloads', () => {
  cy.task('moveDownloads', `${Cypress.spec.name}/${Cypress.currentTest.title}`)
})

Cypress.Commands.add('expectCacheHit', (uri) => {
  cy.request(Cypress.env('API_ROOT_URL_CACHED') + uri + '.jsonhal').then((response) => {
    const headers = response.headers
    expect(headers['x-cache']).to.eq('HIT')
  })
})

Cypress.Commands.add('expectCacheMiss', (uri) => {
  cy.request(Cypress.env('API_ROOT_URL_CACHED') + uri + '.jsonhal').then((response) => {
    const headers = response.headers
    expect(headers['x-cache']).to.eq('MISS')
  })
})

Cypress.Commands.add('expectCachePass', (uri) => {
  cy.request(Cypress.env('API_ROOT_URL_CACHED') + uri + '.jsonhal').then((response) => {
    const headers = response.headers
    expect(headers['x-cache']).to.eq('PASS')
  })
})

Cypress.Commands.add('apiPatch', (uri, body) => {
  cy.request({
    method: 'PATCH',
    url: Cypress.env('API_ROOT_URL_CACHED') + uri + '.jsonhal',
    body,
    headers: {
      'Content-Type': 'application/merge-patch+json',
    },
  })
})

Cypress.Commands.add('apiPost', (uri, body) => {
  cy.request({
    method: 'POST',
    url: Cypress.env('API_ROOT_URL_CACHED') + uri + '.jsonhal',
    body,
    headers: {
      'Content-Type': 'application/hal+json',
    },
  })
})

Cypress.Commands.add('apiDelete', (uri) => {
  cy.request({
    method: 'DELETE',
    url: Cypress.env('API_ROOT_URL_CACHED') + uri + '.jsonhal',
  })
})
