import {
  bipiUser,
  bruceWayneUser,
  cachedEndpoint,
  felicitySmoakUser,
  grgrCampId,
  grgrPeriodId,
  loremIpsumCampId,
  skilagerCampId,
} from '../constants'
import collectionResponse from './responses/activities_collection.json'

const collectionXKeys =
  /* campCollaboration for bipiUser */
  '10d8f02ce5b4 ' +
  /**
   * activitiy "Snowboardfahren"
   */
  'a13fadc97610 a13fadc97610#scheduleEntries a13fadc97610#camp a13fadc97610#category a13fadc97610#progressLabel a13fadc97610#activityResponsibles a13fadc97610#rootContentNode ' +
  /* embedded progress labels: */
  'af92782262d7 af92782262d7#camp a13fadc97610#embeddedProgressLabel ' +
  /* embedded schedule entries: */
  /* (the first schedule entry also includes the period id) */
  '29c9e9a07d82 7fa4564a5d5d 29c9e9a07d82#period 29c9e9a07d82#activity 29c9e9a07d82#day ' +
  'f08d69cae18a f08d69cae18a#period f08d69cae18a#activity f08d69cae18a#day ' +
  '7e8086d94633 7e8086d94633#period 7e8086d94633#activity 7e8086d94633#day ' +
  'a13fadc97610#embeddedScheduleEntries ' +
  /* embedded activitiy responsibles: */
  '06743ccfeedd 06743ccfeedd#activity 06743ccfeedd#campCollaboration ' +
  '21bc6661c569 21bc6661c569#activity 21bc6661c569#campCollaboration ' +
  'a13fadc97610#embeddedActivityResponsibles a13fadc97610#contentNodes ' +
  /**
   * activity "Skifahren"
   */
  'b29d387cc403 b29d387cc403#scheduleEntries b29d387cc403#camp b29d387cc403#category b29d387cc403#progressLabel b29d387cc403#activityResponsibles ' +
  /* embedded progress labels: */
  'b29d387cc403#rootContentNode b29d387cc403#embeddedProgressLabel ' +
  /* embedded schedule entries: */
  'e68f4e47517a e68f4e47517a#period e68f4e47517a#activity e68f4e47517a#day ' +
  'f0883e931649 f0883e931649#period f0883e931649#activity f0883e931649#day ' +
  'ee85308a97d1 ee85308a97d1#period ee85308a97d1#activity ee85308a97d1#day ' +
  'f89a1501dbb6 f89a1501dbb6#period f89a1501dbb6#activity f89a1501dbb6#day ' +
  /* embedded activitiy responsibles: */
  'b29d387cc403#embeddedScheduleEntries ' +
  'a9a760e36fd8 a9a760e36fd8#activity a9a760e36fd8#campCollaboration b29d387cc403#embeddedActivityResponsibles ' +
  'b29d387cc403#contentNodes ' +
  /* collection URI (for detecting addition of new activities) */
  '/api/camps/70ca971c992f/activities'

describe('cache test: /camps/{campId}/activities', () => {
  it('caches /camps/{campId}/activities separately for each login', () => {
    const uri = `/api/camps/${skilagerCampId}/activities`

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

  it('invalidates /camps/{campId}/activities for all users on activity patch', () => {
    const uri = `/api/camps/${loremIpsumCampId}/activities`
    const activityId = '3d1e5c91ceb2'

    // bring data into defined state
    Cypress.session.clearAllSavedSessions()
    cy.login(bruceWayneUser)
    cy.apiPatch(`/api/activities/${activityId}`, {
      title: 'Breakfast',
    })

    // warm up cache
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)

    cy.login(felicitySmoakUser)
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // touch activity
    cy.apiPatch(`/api/activities/${activityId}`, {
      title: 'Frühstück',
    })

    // ensure cache was invalidated
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)

    cy.login(bruceWayneUser)
    cy.expectCacheMiss(uri)
  })

  it('invalidates /camps/{campId}/activities for new activity', () => {
    const uri = `/api/camps/${grgrCampId}/activities`

    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)

    // warm up cache
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // add new activity to camp
    cy.apiPost('/api/activities', {
      title: 'New_activity',
      category: '/api/categories/1a869b162875',
      scheduleEntries: [
        {
          period: '/periods/76be24bce434',
          start: '2025-05-10T08:00:00+00:00',
          end: '2025-05-10T09:00:00+00:00',
        },
      ],
    }).then((response) => {
      const newActivityUri = response.body._links.self.href

      // ensure cache was invalidated
      cy.waitForCacheMiss(uri)
      cy.expectCacheHit(uri)

      // delete newly created contentNode
      cy.apiDelete(newActivityUri)

      // ensure cache was invalidated
      cy.waitForCacheMiss(uri)
      cy.expectCacheHit(uri)
    })
  })

  it('invalidates /camps/{campId}/activities when adding a scheduleEntry', () => {
    const uri = `/api/camps/${grgrCampId}/activities`

    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)

    // warm up cache
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // add new scheduleEntry
    cy.apiPost('/api/schedule_entries', {
      activity: '/activities/ffd08c52288c',
      end: '2025-05-11T06:00:00+00:00',
      period: '/periods/76be24bce434',
      start: '2025-05-11T05:00:00+00:00',
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

  it('invalidates /camps/{campId}/activities when patching a progress label', () => {
    const uri = `/api/camps/${grgrCampId}/activities`
    const progressLabelId = '82547049ea38'

    // bring data into defined state
    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)
    cy.apiPatch(`/api/activity_progress_labels/${progressLabelId}`, {
      title: 'Planned',
    })

    // warm up cache
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)

    // touch progress label
    cy.apiPatch(`/api/activity_progress_labels/${progressLabelId}`, {
      title: 'Geplant',
    })

    // ensure cache was invalidated
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)
  })

  it('invalidates /camps/{campId}/activities when adding an activity responsible', () => {
    const uri = `/api/camps/${grgrCampId}/activities`

    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)

    // warm up cache
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // add new scheduleEntry
    cy.apiPost('/api/activity_responsibles', {
      activity: '/activities/ffd08c52288c',
      campCollaboration: '/camp_collaborations/b0bdb7202a9d',
    }).then((response) => {
      const newActivityResponsibleUri = response.body._links.self.href

      // ensure cache was invalidated
      cy.waitForCacheMiss(uri)
      cy.expectCacheHit(uri)

      // delete newly created scheduleEntry
      cy.apiDelete(newActivityResponsibleUri)

      // ensure cache was invalidated
      cy.waitForCacheMiss(uri)
      cy.expectCacheHit(uri)
    })
  })

  it('invalidates /camps/{campId}/activities when changing the period dates (moveScheduleEntries: true)', () => {
    const uri = `/api/camps/${grgrCampId}/activities`

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

  it('invalidates /camps/{campId}/activities when changing the period dates (moveScheduleEntries: false)', () => {
    const uri = `/api/camps/${grgrCampId}/activities`

    Cypress.session.clearAllSavedSessions()
    cy.login(bipiUser)

    // warm up cache
    cy.expectCacheMiss(uri)
    cy.expectCacheHit(uri)

    // move period start date
    cy.apiPatch(`/api/periods/${grgrPeriodId}`, {
      start: '2025-05-09',
      moveScheduleEntries: false,
    })

    // ensure cache was invalidated
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)

    // move period start date
    cy.apiPatch(`/api/periods/${grgrPeriodId}`, {
      start: '2025-05-10',
      moveScheduleEntries: false,
    })

    // ensure cache was invalidated
    cy.waitForCacheMiss(uri)
    cy.expectCacheHit(uri)
  })
})
