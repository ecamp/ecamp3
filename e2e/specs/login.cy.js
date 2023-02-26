// https://docs.cypress.io/api/introduction/api.html

describe('Login test', () => {
  it('displays the login page', () => {
    cy.visit('/')
    cy.contains('Login')
    cy.contains('This is the development version of eCamp v3.')
    cy.contains('Register now')
  })

  it('can login with default user', () => {
    cy.visit('/')

    cy.get('[type="text"]').type('test@example.com')
    cy.get('[type="password"]').type('test')
    cy.get('[type="submit').click()

    cy.location('pathname', { timeout: 60000 }).should('include', '/camps')

    cy.contains('My Camps')
    cy.contains('GRGR')
    cy.contains('Harry Potter Lager')
  })
})
