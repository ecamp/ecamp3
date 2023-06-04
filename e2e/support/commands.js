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
