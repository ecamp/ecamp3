import {
  bipiUser,
  bruceWayneUser,
  cachedEndpoint,
  castorUser,
  felicitySmoakUser,
  grgrCampId,
  loremIpsumCampId,
} from '../constants'
import collectionResponse from './responses/categories_collection.json'

const collectionXKeys =
  /* campCollaboration for bipiUser */
  'b0bdb7202a9d ' +
  /* Category ES */
  'ebfd46a1c181 ebfd46a1c181#camp ebfd46a1c181#preferredContentTypes ebfd46a1c181#rootContentNode ebfd46a1c181#contentNodes ' +
  /* Category LA */
  '1a869b162875 1a869b162875#camp 1a869b162875#preferredContentTypes 1a869b162875#rootContentNode 1a869b162875#contentNodes ' +
  /* Category LP */
  'dfa531302823 dfa531302823#camp dfa531302823#preferredContentTypes dfa531302823#rootContentNode dfa531302823#contentNodes ' +
  /* Category LS */
  'a023e85227ac a023e85227ac#camp a023e85227ac#preferredContentTypes a023e85227ac#rootContentNode a023e85227ac#contentNodes ' +
  /* collection URI (for detecting addition of new categories) */
  '/api/camps/3c79b99ab424/categories'

describe('cache test: /camps/{campId}/categories', () => {
  it('caches /camps/{campId}/categories separately for each login', () => {
    const uri = `/api/camps/${grgrCampId}/categories`

    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)

    // first request is a cache miss
    cy.request(`${cachedEndpoint}${uri}.jsonhal`).then((response) => {
      const headers = response.headers
      expect(headers.xkey).to.eq(collectionXKeys)
      expect(headers['x-cache']).to.eq('MISS')
      expect(response.body).to.deep.equal(collectionResponse)
    })

    // second request is a cache hit
    cy.expectCacheHit(uri)

    // request with a new user is a cache miss
    cy.login(castorUser)
    cy.expectCacheMiss(uri)
  })

  it('invalidates /camps/{campId}/categories for all users on category patch', () => {
    const uri = `/api/camps/${loremIpsumCampId}/categories`

    // bring data into defined state
    Cypress.session.clearAllSavedSessions()
    cy.login(bruceWayneUser)
    cy.apiPatch('/api/categories/c5e1bc565094', {
      name: 'old_name',
    })

    // warm up cache
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)

    cy.login(felicitySmoakUser)
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // touch category
    cy.apiPatch('/api/categories/c5e1bc565094', {
      name: 'new_name',
    })

    // ensure cache was invalidated
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)

    cy.login(bruceWayneUser)
    cy.expectCacheMiss(uri)
  })

  it('invalidates /camps/{campId}/categories for new category', () => {
    const uri = `/api/camps/${grgrCampId}/categories`

    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)

    // warm up cache
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // add new category to camp
    cy.apiPost('/api/categories', {
      camp: `/api/camps/${grgrCampId}`,
      short: 'new',
      name: 'new Category',
      color: '#000000',
      numberingStyle: '1',
    }).then((response) => {
      const newContentNodeUri = response.body._links.self.href

      // ensure cache was invalidated
      cy.waitForCacheMiss(uri)
      cy.expectCacheHit(uri)

      // delete newly created contentNode
      cy.apiDelete(newContentNodeUri)

      // ensure cache was invalidated
      cy.waitForCacheMiss(uri)
      cy.expectCacheHit(uri)
    })
  })

  it('invalidates cached data when user leaves a camp', () => {
    Cypress.session.clearAllSavedSessions()
    const uri = `/api/camps/${grgrCampId}/categories`

    cy.intercept('PATCH', '/api/camp_collaborations/**').as('camp_collaboration')
    cy.intercept('PATCH', '/api/invitations/**').as('invitations')

    // warm up cache
    cy.login(castorUser)
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // deactivate Castor
    cy.login(bipiUser)
    cy.visit(`/camps/${grgrCampId}/GRGR/admin/collaborators`)
    cy.get('.v-list-item__title:contains("Castor")').click()
    cy.get('button:contains("Deaktivieren")').click()
    cy.get('div[role=alert]').find('button').contains('Deaktivieren').click()
    cy.wait('@camp_collaboration')

    // ensure cache was invalidated
    cy.login(castorUser)
    cy.request({
      url: `${cachedEndpoint}${uri}.jsonhal`,
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(404)
    })

    // delete old emails
    cy.request({
      url: '/mail/email/all',
      method: 'DELETE',
    })

    // invite Castor
    cy.login(bipiUser)
    cy.visit(`/camps/${grgrCampId}/GRGR/admin/collaborators`)
    cy.get('.v-list-item__title:contains("Castor")').click()
    cy.get('button:contains("Erneut einladen")').click()
    cy.wait('@camp_collaboration')

    // accept invitation as Castor
    cy.login(castorUser)

    cy.request({
      url: '/mail/email',
    }).then((response) => {
      const emailHtmlContent = response.body[0].html
      cy.document().then((document) => {
        document.documentElement.innerHTML = emailHtmlContent
      })

      cy.get('a:contains("Einladung beantworten")').invoke('removeAttr', 'target').click()

      cy.get('button:contains("Einladung mit aktuellem Account akzeptieren")').click()
      cy.wait('@invitations')
      cy.visit('/camps')
      cy.contains('GRGR')
    })
  })
})
