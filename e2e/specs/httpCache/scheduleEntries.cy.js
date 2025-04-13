import {
  bipiUser,
  bruceWayneUser,
  cachedEndpoint,
  castorUser,
  skilagerPeriodId,
  grgrPeriodId,
  harryMainPeriodId,
  harrySecondPeriodId,
} from '../constants'
import collectionResponse from './responses/schedule_entries_collection.json'

const collectionXKeys =
  /* campCollaboration for bipiUser */
  '10d8f02ce5b4 ' +
  /* scheduleEntries + links */
  /* the first scheduleEntry also includes the period id 7fa4564a5d5d */
  'e68f4e47517a 7fa4564a5d5d e68f4e47517a#day ' +
  'f0883e931649 f0883e931649#day ' +
  '29c9e9a07d82 29c9e9a07d82#day ' +
  'ee85308a97d1 ee85308a97d1#day ' +
  'f08d69cae18a f08d69cae18a#day ' +
  '7e8086d94633 7e8086d94633#day ' +
  'f89a1501dbb6 f89a1501dbb6#day ' +
  /* collection URI (for detecting addition of new schedule entries) */
  '/api/periods/7fa4564a5d5d/schedule_entries'

describe('cache test: /periods/{periodId}/scheduleEntries', () => {
  it('caches /periods/{periodId}/schedule_entries separately for each login', () => {
    const uri = `/api/periods/${skilagerPeriodId}/schedule_entries`

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
    cy.login(bruceWayneUser)
    cy.expectCacheMiss(uri)
  })

  it('invalidates /periods/{periodId}/schedule_entries for all users on scheduleEntry patch', () => {
    const uri = `/api/periods/${grgrPeriodId}/schedule_entries`
    const scheduleEntryId = '12f34c89ce11'

    // bring data into defined state
    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)
    cy.apiPatch(`/api/schedule_entries/${scheduleEntryId}`, {
      start: '2025-05-10T16:00:00+00:00',
    })

    // warm up cache
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)

    cy.login(castorUser)
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // touch scheduleEntry
    cy.apiPatch(`/api/schedule_entries/${scheduleEntryId}`, {
      start: '2025-05-10T17:00:00+00:00',
    })

    // ensure cache was invalidated
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)

    cy.login(bipiUser)
    cy.expectCacheMiss(uri)
  })

  it('invalidates /periods/{periodId}/schedule_entries for new scheduleEntry', () => {
    const uri = `/api/periods/${grgrPeriodId}/schedule_entries`

    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)

    // warm up cache
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // add new scheduleEntry to period
    cy.apiPost('/api/schedule_entries', {
      start: '2025-05-10T10:00:00+00:00',
      end: '2025-05-10T11:00:00+00:00',
      period: `/api/periods/${grgrPeriodId}`,
      activity: `/api/activities/ffd08c52288c`,
    }).then((response) => {
      const newScheduleEntryUri = response.body._links.self.href

      // ensure cache was invalidated
      cy.waitForCacheMiss(uri)
      cy.expectCacheHit(uri)

      // delete newly created scheduleEntry
      cy.apiDelete(newScheduleEntryUri)

      // ensure cache was invalidated
      cy.waitForCacheMiss(uri)
      cy.expectCacheHit(uri)
    })
  })

  it('invalidates /periods/{periodId}/schedule_entries when moving a schedule entry to another period', () => {
    const uri1 = `/api/periods/${harryMainPeriodId}/schedule_entries`
    const uri2 = `/api/periods/${harrySecondPeriodId}/schedule_entries`
    const scheduleEntryId = '9a4173c9bb73'

    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)

    // warm up cache
    cy.expectCacheMiss(uri1)
    cy.expectCacheMiss(uri2)
    cy.expectCacheHit(uri1)
    cy.expectCacheHit(uri2)

    // move scheduleEntry to 2nd period
    cy.apiPatch(`/api/schedule_entries/${scheduleEntryId}`, {
      start: '2025-08-09T15:00:00+00:00',
      end: '2025-08-09T17:00:00+00:00',
      period: `/periods/${harrySecondPeriodId}`,
    })

    // ensure cache was invalidated
    cy.waitForCacheMiss(uri1)
    cy.waitForCacheMiss(uri2)
    cy.expectCacheHit(uri1)
    cy.expectCacheHit(uri2)

    // move scheduleEntry back
    cy.apiPatch(`/api/schedule_entries/${scheduleEntryId}`, {
      start: '2025-07-20T15:00:00+00:00',
      end: '2025-07-20T17:00:00+00:00',
      period: `/periods/${harryMainPeriodId}`,
    })

    // ensure cache was invalidated
    cy.waitForCacheMiss(uri1)
    cy.waitForCacheMiss(uri2)
    cy.expectCacheHit(uri1)
    cy.expectCacheHit(uri2)
  })

  it('invalidates /periods/{periodId}/schedule_entries when changing the period dates', () => {
    const uri = `/api/periods/${grgrPeriodId}/schedule_entries`

    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)

    // warm up cache
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // move period start date
    cy.apiPatch(`/api/periods/${grgrPeriodId}`, {
      start: '2025-05-09',
      end: '2025-05-12',
      moveScheduleEntries: true,
    })

    // ensure cache was invalidated
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)

    // move period start date
    cy.apiPatch(`/api/periods/${grgrPeriodId}`, {
      start: '2025-05-10',
      end: '2025-05-13',
      moveScheduleEntries: true,
    })

    // ensure cache was invalidated
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)
  })
})
