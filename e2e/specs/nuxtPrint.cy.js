// https://docs.cypress.io/api/introduction/api.html

const path = require('path')

describe('Nuxt print test', () => {
  it('shows print preview', () => {
    cy.login('test@example.com')

    cy.request(Cypress.env('API_ROOT_URL') + '/camps.jsonhal').then((response) => {
      const body = response.body
      const camp = body._embedded.items.filter((c) => c.motto)[0]
      const campUri = camp._links.self.href

      const printConfig = {
        language: 'en',
        documentName: 'camp',
        camp: campUri,
        contents: [
          {
            type: 'Cover',
            options: {},
          },
        ],
      }

      cy.visit(
        Cypress.env('PRINT_URL') +
          '/?config=' +
          encodeURIComponent(JSON.stringify(printConfig))
      )
      cy.contains(camp.title)
      cy.contains(camp.motto)

      cy.get('#content_0_cover').should('have.css', 'font-size', '50px') // this ensures Tailwind is properly built and integrated
    })
  })

  it('downloads PDF', () => {
    cy.task('deleteDownloads')
    cy.login('test@example.com')

    cy.visit('/camps')
    cy.get('a:contains("GRGR")').click()
    cy.get('a:contains("Admin")').click()
    cy.get('a:contains("Drucken")').click()
    cy.get('button:contains("PDF herunterladen (Layout #1)")').click()

    const downloadsFolder = Cypress.config('downloadsFolder')
    cy.readFile(path.join(downloadsFolder, 'Pfila-2023.pdf'), {
      timeout: 30000,
    })
    cy.moveDownloads()
  })
})
