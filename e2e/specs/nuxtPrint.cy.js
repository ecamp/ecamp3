// https://docs.cypress.io/api/introduction/api.html

const path = require('path')

describe('Nuxt print test', () => {
  it('shows print preview', () => {
    cy.login('test@example.com')

    cy.request(Cypress.env('API_ROOT_URL') + '/camps.jsonhal').then((response) => {
      const campUri = response.body._links.items[1].href

      let printConfig = {
        language: 'en',
        documentName: 'Harry Potter Lager.pdf',
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
      cy.contains('Sola 2023')
      cy.contains('Title: Harry Potter Lager')
      cy.contains('Motto: Harry Potter')

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
    cy.task('deleteDownloads')
  })
})
