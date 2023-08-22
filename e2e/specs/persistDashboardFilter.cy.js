describe('The filters in the dashboard', () => {
  beforeEach(() => {
    cy.login('test@example.com')
    cy.visit('/camps')
    cy.get('a:contains("GRGR")').click()
  })

  afterEach(() => {
    /**
     * Firefox does not like it if a test is finished while
     * requests are still running. Thus we wait for this text to be rendered.
     * This worked better than intercepting requests.
     */
    cy.contains('Hauptlager')
  })

  it('can be shared via url', () => {
    cy.get('span.v-chip:contains("Kategorie")').click()
    clickOnItemWithLabel('Essen')
    clickOnItemWithLabel('Lagersport')

    cy.get('span.v-chip:contains("Kategorie: ES oder LS")').should('exist')

    cy.get('span.v-chip:contains("Status")').click()
    clickOnItemWithLabel('Geplant')
    clickOnItemWithLabel('Coach OK')

    cy.get('span.v-chip:contains("Status: Geplant oder Coach OK")').should('exist')

    cy.url().then((url) => cy.visit(url))

    cy.get('span.v-chip:contains("Kategorie: ES oder LS")')
    cy.get('span.v-chip:contains("Status: Geplant oder Coach OK")')
  })

  it('are removed from the url when removed in the gui', () => {
    cy.get('span.v-chip:contains("Kategorie")').click()
    clickOnItemWithLabel('Essen')
    clickOnItemWithLabel('Lagersport')

    cy.get('span.v-chip:contains("Kategorie: ES oder LS")').should('exist')

    cy.url().then((url) => cy.visit(url))

    cy.get('span.v-chip:contains("Kategorie: ES oder LS")').should('exist')

    cy.get('span.v-chip:contains("Kategorie: ES oder LS")').click()

    clickOnItemWithLabel('Essen')
    clickOnItemWithLabel('Lagersport')

    cy.get('span.v-chip:contains("Kategorie: ES oder LS")').should('not.exist')
    cy.get('span.v-chip:contains("Kategorie")').should('exist')

    cy.url().then((url) => cy.visit(url))

    cy.get('span.v-chip:contains("Kategorie: ES oder LS")').should('not.exist')
    cy.get('span.v-chip:contains("Kategorie")').should('exist')
  })

  it('support selecting activities without responsibles', () => {
    cy.get('span.v-chip:contains("Verantwortlich")').click()
    clickOnItemWithLabel('Keine Verantwortlichen')

    cy.get('span.v-chip:contains("Verantwortlich: Keine Verantwortlichen")')

    cy.url().then((url) => cy.visit(url))

    cy.get('span.v-chip:contains("Verantwortlich: Keine Verantwortlichen")')
  })
})

function clickOnItemWithLabel(label) {
  cy.get(`div.v-list-item:contains("${label}")`)
    .find('.v-input--selection-controls__ripple')
    .click()
}
