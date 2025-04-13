import { bipiUser, cachedEndpoint, castorUser, basiskursCampId } from '../constants'
import collectionResponse from './responses/checklists_collection.json'

const collectionXKeys =
  /* campCollaboration for bipiUser */
  '146c0608237f ' +
  /* checklist entry */
  'ebbd0c61eb85 ebbd0c61eb85#camp ' +
  /* collection URI (for detecting addition of new checklists) */
  '/api/camps/5d28f99890bc/checklists'

describe('cache test: /camps/checklists', () => {
  it('caches /camp/{campId}/checklists separately for each login', () => {
    const uri = `/api/camps/${basiskursCampId}/checklists`

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

  it('invalidates /camp/{campId}/checklists on checklist patch', () => {
    const uri = `/api/camps/${basiskursCampId}/checklists`

    // bring data into defined state
    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)
    cy.apiPatch('/api/checklists/ebbd0c61eb85', {
      name: 'Training targets',
    })

    // warm up cache
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)

    // touch checklist
    cy.apiPatch('/api/checklists/ebbd0c61eb85', {
      name: 'Ausbildungsziele',
    })

    // ensure cache was invalidated
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)
  })

  it('invalidates /camp/{campId}/checklists for new checklist', () => {
    const uri = `/api/camps/${basiskursCampId}/checklists`

    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)

    // warm up cache
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // add new checklist to camp
    cy.apiPost('/api/checklists', {
      camp: `/api/camps/${basiskursCampId}`,
      name: 'new_checklist',
    }).then((response) => {
      const newChecklistUri = response.body._links.self.href

      // ensure cache was invalidated
      cy.waitForCacheMiss(uri)
      cy.expectCacheHit(uri)

      // delete newly created contentNode
      cy.apiDelete(newChecklistUri)

      // ensure cache was invalidated
      cy.waitForCacheMiss(uri)
      cy.expectCacheHit(uri)
    })
  })
})
