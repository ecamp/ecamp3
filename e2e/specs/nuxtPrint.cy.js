// https://docs.cypress.io/api/introduction/api.html

const path = require('path')

describe('Nuxt print test', () => {
  it('shows print preview', () => {
    cy.login('test@example.com')

    cy.request(Cypress.env('API_ROOT_URL') + '/camps.jsonhal').then((response) => {
      const body = response.body
      const campUri = body._links.items[1].href
      const camp = body._embedded.items[1]

      let printConfig = {
        language: 'en',
        documentName: 'camp.pdf',
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
      cy.contains(camp.name)
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
    cy.get('a:contains("Print")').click()
    cy.get('button:contains("Download PDF (layout #1)")').click()

    const downloadsFolder = Cypress.config('downloadsFolder')
    cy.readFile(path.join(downloadsFolder, 'GRGR.pdf'), { timeout: 30000 })
    cy.moveDownloads()
  })
})
