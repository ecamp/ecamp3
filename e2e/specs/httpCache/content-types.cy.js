import collectionResponse from './responses/content_types_collection.json'
import itemResponse from './responses/content_types_entity.json'
import { bipiUser, cachedEndpoint, castorUser } from '../constants'

const collectionXKeys =
  'a4211c11211c c462edd869f3 5e2028c55ee4 a4211c112939 f17470519474 1a0f84e322c8 3ef17bd1df72 4f0c657fecef 44dcc7493c65 cfccaecd4bad 318e064ea0c9 /api/content_types'

describe('cache test: /content-types', () => {
  beforeEach(() => {
    cy.wrap(Cypress.session.clearAllSavedSessions())
  })

  it('caches collection separately for each login', () => {
    const uri = '/api/content_types'

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

  it('caches item', () => {
    const contentTypeId = '318e064ea0c9'
    const uri = `/api/content_types/${contentTypeId}`

    cy.login(bipiUser)

    // first request is a cache miss
    cy.request(`${cachedEndpoint}${uri}.jsonhal`).then((response) => {
      const headers = response.headers
      expect(headers.xkey).to.eq(contentTypeId)
      expect(headers['x-cache']).to.eq('MISS')
      expect(response.body).to.deep.equal(itemResponse)
    })

    // second request is a cache hit
    cy.expectCacheHit(uri)
  })
})
