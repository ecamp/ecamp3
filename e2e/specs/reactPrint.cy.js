// https://docs.cypress.io/api/introduction/api.html

const path = require('path')

describe('React print test', () => {
  it('downloads PDF', () => {
    cy.task('deleteDownloads')
    cy.login('test@example.com')

    cy.visit('/camps')
    cy.get('a:contains("GRGR")').click()
    cy.location('pathname')
      .should('contain', '/dashboard')
      .then((pathname) => {
        const newPath = pathname.replace('/dashboard', '/print')
        return cy.visit(newPath)
      })
    cy.location('pathname').should('contain', '/print')
    cy.get('button:contains("#2")').click()

    const downloadsFolder = Cypress.config('downloadsFolder')
    cy.readFile(path.join(downloadsFolder, 'Pfila-2023.pdf'), { timeout: 30000 })
    cy.moveDownloads()
  })
})
