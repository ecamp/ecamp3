// https://docs.cypress.io/api/introduction/api.html

const path = require('path')

describe('Client print test', () => {
  it('downloads PDF', () => {
    cy.task('deleteDownloads')
    cy.login('test@example.com')

    cy.visit('/camps')
    cy.get('a:contains("GRGR")').click()
    cy.get('a:contains("Print")').click()
    cy.get('button:contains("Download PDF (layout #2)")').click()

    const downloadsFolder = Cypress.config('downloadsFolder')
    cy.readFile(path.join(downloadsFolder, 'Pfila-2023.pdf'), {
      timeout: 30000,
    })
    cy.moveDownloads()
  })
})
