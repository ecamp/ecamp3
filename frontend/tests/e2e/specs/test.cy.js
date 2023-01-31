// https://docs.cypress.io/api/introduction/api.html

describe('Smoke test', () => {
  it('displays the login page', () => {
    cy.visit('/')
    cy.contains('Login')
    cy.contains('This is the development version of eCamp v3.')
    cy.contains('Register now')
  })
})
