describe('Persist Dashboard Filter', () => {
  it('should add Category-Filters to query', () => {
    cy.login('test@example.com')
    cy.visit('/camps')
    cy.get('a:contains("GRGR")').click()
    cy.get('span.v-chip:contains("Category")').click()
    clickOnItemWithLabel("Essen")
    clickOnItemWithLabel("Lagersport")
    cy.wait(500)
    cy.url().should('include','category')
    cy.url().should('include','6adced5270de')
    cy.url().should('include','9af703a10a9c')
  })
  it('should remove Category-Filters from query', () => {
    cy.login('test@example.com')
    cy.visit('/camps')
    cy.get('a:contains("GRGR")').click()
    cy.get('span.v-chip:contains("Category")').click()
    clickOnItemWithLabel("Essen")
    clickOnItemWithLabel("Lagersport")
    cy.wait(500)
    clickOnItemWithLabel("Essen")
    clickOnItemWithLabel("Lagersport")
    cy.wait(500)

    cy.url().should('not.include','category')
    cy.url().should('not.include','6adced5270de')
    cy.url().should('not.include','9af703a10a9c')
  })
  it('should add Filters to query', () => {
    cy.login('test@example.com')
    cy.visit('/camps')
    cy.get('a:contains("GRGR")').click()
    cy.get('span.v-chip:contains("Category")').click()
    clickOnItemWithLabel("Essen")
    clickOnItemWithLabel("Lagersport")
    cy.wait(500)
    cy.get('span.v-chip:contains("State")').click()
    clickOnItemWithLabel("Geplant")
    clickOnItemWithLabel("Coach OK")
    cy.wait(500)


    cy.url().should('include','category')
    cy.url().should('include','6adced5270de')
    cy.url().should('include','9af703a10a9c')

    cy.url().should('include','progressLabel')
    cy.url().should('include','56d24f5359de')
    cy.url().should('include','c50da81e0cfc')

    cy.reload(true)
    cy.wait(500)

    cy.get('span.v-chip:contains("Category: ES or LS")')
    cy.get('span.v-chip:contains("State: Geplant or Coach OK")')
  })
  it('should add Responsible-Filters to query', () => {
    cy.login('test@example.com')
    cy.visit('/camps')
    cy.get('a:contains("GRGR")').click()
    cy.get('span.v-chip:contains("Responsible")').click()
    clickOnItemWithLabel("No responsibles")
    cy.url().should('include','responsible=none')
    cy.wait(500)
    cy.reload(true)
    cy.wait(10000)
    cy.get('span.v-chip:contains("Responsible: No responsibles")')
  })
})

function clickOnItemWithLabel(label) {
  cy.get(`div.v-list-item:contains("${label}")`).find('.v-input--selection-controls__ripple').click()
}
