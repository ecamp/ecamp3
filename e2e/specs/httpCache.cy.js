describe('HTTP cache tests', () => {
  it('caches /content_types separately for each login', () => {
    const uri = '/api/content_types'

    Cypress.session.clearAllSavedSessions()
    cy.login('test@example.com')

    // first request is a cache miss
    cy.request(Cypress.env('API_ROOT_URL_CACHED') + uri + '.jsonhal').then((response) => {
      const headers = response.headers
      expect(headers.xkey).to.eq(
        'a4211c11211c c462edd869f3 5e2028c55ee4 a4211c112939 f17470519474 1a0f84e322c8 3ef17bd1df72 4f0c657fecef 44dcc7493c65 cfccaecd4bad 318e064ea0c9 /api/content_types'
      )
      expect(headers['x-cache']).to.eq('MISS')
      cy.readFile('./specs/responses/content_types_collection.json').then((data) =>
        expect(response.body).to.deep.equal(data)
      )
    })

    // second request is a cache hit
    cy.expectCacheHit(uri)

    // request with a new user is a cache miss
    cy.login('castor@example.com')
    cy.expectCacheMiss(uri)
  })

  it('caches /content_types/318e064ea0c9', () => {
    const uri = '/api/content_types/318e064ea0c9'

    Cypress.session.clearAllSavedSessions()
    cy.login('test@example.com')

    // first request is a cache miss
    cy.request(Cypress.env('API_ROOT_URL_CACHED') + uri + '.jsonhal').then((response) => {
      const headers = response.headers
      expect(headers.xkey).to.eq('318e064ea0c9')
      expect(headers['x-cache']).to.eq('MISS')
      cy.readFile('./specs/responses/content_types_entity.json').then((data) =>
        expect(response.body).to.deep.equal(data)
      )
    })

    // second request is a cache hit
    cy.expectCacheHit(uri)
  })

  it('caches /camp/{campId}/categories separately for each login', () => {
    const uri = '/api/camps/3c79b99ab424/categories'

    Cypress.session.clearAllSavedSessions()
    cy.login('test@example.com')

    // first request is a cache miss
    cy.request(Cypress.env('API_ROOT_URL_CACHED') + uri + '.jsonhal').then((response) => {
      const headers = response.headers
      expect(headers.xkey).to.eq(
        /* campCollaboration for test@example.com */
        'b0bdb7202a9d ' +
          /* Category ES */
          'ebfd46a1c181 ebfd46a1c181#camp ebfd46a1c181#preferredContentTypes 9d7b3a220fb4 9d7b3a220fb4#root 9d7b3a220fb4#parent 9d7b3a220fb4#children 9d7b3a220fb4#contentType ebfd46a1c181#rootContentNode ebfd46a1c181#contentNodes ' +
          /* Category LA */
          '1a869b162875 1a869b162875#camp 1a869b162875#preferredContentTypes be9b6b7f23f6 be9b6b7f23f6#root be9b6b7f23f6#parent be9b6b7f23f6#children be9b6b7f23f6#contentType 1a869b162875#rootContentNode 1a869b162875#contentNodes ' +
          /* Category LP */
          'dfa531302823 dfa531302823#camp dfa531302823#preferredContentTypes 63cbc734fa04 63cbc734fa04#root 63cbc734fa04#parent 63cbc734fa04#children 63cbc734fa04#contentType dfa531302823#rootContentNode dfa531302823#contentNodes ' +
          /* Category LS */
          'a023e85227ac a023e85227ac#camp a023e85227ac#preferredContentTypes 2cce9e17a368 2cce9e17a368#root 2cce9e17a368#parent 2cce9e17a368#children 2cce9e17a368#contentType a023e85227ac#rootContentNode a023e85227ac#contentNodes ' +
          /* collection URI (for detecting addition of new categories) */
          '/api/camps/3c79b99ab424/categories'
      )
      expect(headers['x-cache']).to.eq('MISS')
      cy.readFile('./specs/responses/categories_collection.json').then((data) =>
        expect(response.body).to.deep.equal(data)
      )
    })

    // second request is a cache hit
    cy.expectCacheHit(uri)

    // request with a new user is a cache miss
    cy.login('castor@example.com')
    cy.expectCacheMiss(uri)
  })

  it('invalidates /camp/{campId}/categories for all users on category patch', () => {
    const uri = '/api/camps/9c2447aefe38/categories'

    // bring data into defined state
    Cypress.session.clearAllSavedSessions()
    cy.login('bruce@wayne.com')
    cy.apiPatch('/api/categories/c5e1bc565094', {
      name: 'old_name',
    })

    // warm up cache
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    cy.login('felicity@smoak.com')
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // touch category
    cy.apiPatch('/api/categories/c5e1bc565094', {
      name: 'new_name',
    })

    // ensure cache was invalidated
    cy.expectCacheMiss(uri)
    cy.login('bruce@wayne.com')
    cy.expectCacheMiss(uri)
  })

  it('invalidates /camp/{campId}/categories for new contentNode child', () => {
    const uri = '/api/camps/3c79b99ab424/categories'

    Cypress.session.clearAllSavedSessions()
    cy.login('test@example.com')

    // warm up cache
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // add new child to root content node (9d7b3a220fb4) of category (ebfd46a1c181)
    cy.apiPost('/api/content_node/column_layouts', {
      parent: '/api/content_node/column_layouts/9d7b3a220fb4',
      slot: '1',
      contentType: '/api/content_types/f17470519474',
    }).then((response) => {
      const newContentNodeUri = response.body._links.self.href

      // ensure cache was invalidated
      cy.expectCacheMiss(uri)
      cy.expectCacheHit(uri)

      // delete newly created contentNode
      cy.apiDelete(newContentNodeUri)

      // ensure cache was invalidated
      cy.expectCacheMiss(uri)
    })
  })

  it('invalidates /camp/{campId}/categories for new category', () => {
    const uri = '/api/camps/3c79b99ab424/categories'

    Cypress.session.clearAllSavedSessions()
    cy.login('test@example.com')

    // warm up cache
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // add new category to camp
    cy.apiPost('/api/categories', {
      camp: '/api/camps/3c79b99ab424',
      short: 'new',
      name: 'new Category',
      color: '#000000',
      numberingStyle: '1',
    }).then((response) => {
      const newContentNodeUri = response.body._links.self.href

      // ensure cache was invalidated
      cy.expectCacheMiss(uri)
      cy.expectCacheHit(uri)

      // delete newly created contentNode
      cy.apiDelete(newContentNodeUri)

      // ensure cache was invalidated
      cy.expectCacheMiss(uri)
    })
  })

  it('invalidates cached data when user leaves a camp', () => {
    Cypress.session.clearAllSavedSessions()
    const uri = '/api/camps/3c79b99ab424/categories'

    cy.intercept('PATCH', '/api/camp_collaborations/**').as('camp_collaboration')
    cy.intercept('PATCH', '/api/invitations/**').as('invitations')

    // warm up cache
    cy.login('castor@example.com')
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // deactivate Castor
    cy.login('test@example.com')
    cy.visit('/camps/3c79b99ab424/GRGR/admin/collaborators')
    cy.get('.v-list-item__title:contains("Castor")').click()
    cy.get('button:contains("Deaktivieren")').click()
    cy.get('div[role=alert]').find('button').contains('Deaktivieren').click()
    cy.wait('@camp_collaboration')

    // ensure cache was invalidated
    cy.login('castor@example.com')
    cy.request({
      url: Cypress.env('API_ROOT_URL_CACHED') + uri + '.jsonhal',
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(404)
    })

    // delete old emails
    cy.request({
      url: 'localhost:3000/mail/email/all',
      method: 'DELETE',
    })

    // invite Castor
    cy.login('test@example.com')
    cy.visit('/camps/3c79b99ab424/GRGR/admin/collaborators')
    cy.get('.v-list-item__title:contains("Castor")').click()
    cy.get('button:contains("Erneut einladen")').click()
    cy.wait('@camp_collaboration')

    // accept invitation as Castor
    cy.login('castor@example.com')

    cy.request({
      url: 'localhost:3000/mail/email',
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

  it("doesn't cache /camps", () => {
    const uri = '/api/camps'
    Cypress.session.clearAllSavedSessions()
    cy.login('test@example.com')
    cy.expectCachePass(uri)
  })
})
