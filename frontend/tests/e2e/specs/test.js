// https://docs.cypress.io/api/introduction/api.html

describe('Smoke test', () => {
  it('displays the login page', () => {
    cy.visit('/')
    cy.contains('Login')
    cy.contains('This is the site of the new eCamp.')
    cy.contains('Register now')
  })
})
