import { test, expect } from '@playwright/test'
import {
  bipiUser,
  bruceWayneUser,
  felicitySmoakUser,
  grgrCampId,
  grgrPeriodId,
  loremIpsumCampId,
  skilagerCampId,
} from '@/utils/constants'
import collectionResponse from '@/test-data/httpCache/activities_collection.json'
import {
  getAuthContext,
  expectCacheHit,
  expectCacheMiss,
  waitForCacheMiss,
  apiGet,
  apiPatch,
  apiPost,
  apiDelete,
} from '@/utils/helpers'

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
  'a13fadc97610#embeddedActivityResponsibles a13fadc97610#emptyContentNodesForIriGeneration ' +
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
  'b29d387cc403#emptyContentNodesForIriGeneration ' +
  /* collection URI (for detecting addition of new activities) */
  '/api/camps/70ca971c992f/activities'

test.describe('cache test: /camps/{campId}/activities', () => {
  test.describe.configure({ mode: 'serial' })

  test('caches /camps/{campId}/activities separately for each login', async () => {
    const uri = `/api/camps/${skilagerCampId}/activities`

    const bipiApi = await getAuthContext(bipiUser)

    // first request is a cache miss
    const request = await apiGet(bipiApi, uri)
    const headers = request.headers()
    expect(headers['xkey']).toBe(collectionXKeys)
    expect(headers['x-cache']).toBe('MISS')
    expect(await request.json()).toEqual(collectionResponse)

    // second request is a cache hit
    await expectCacheHit(bipiApi, uri)

    // request with a new user is a cache miss
    const bruceApi = await getAuthContext(bruceWayneUser)
    await expectCacheMiss(bruceApi, uri)
  })

  test('invalidates /camps/{campId}/activities for all users on activity patch', async () => {
    const uri = `/api/camps/${loremIpsumCampId}/activities`
    const activityId = '3d1e5c91ceb2'

    // bring data into defined state
    const bruceApi = await getAuthContext(bruceWayneUser)
    await apiPatch(bruceApi, `/api/activities/${activityId}`, {
      title: 'Breakfast',
    })

    // warm up cache
    await apiGet(bruceApi, uri)
    await expectCacheHit(bruceApi, uri)

    const felicityApi = await getAuthContext(felicitySmoakUser)
    await expectCacheMiss(felicityApi, uri)
    await expectCacheHit(felicityApi, uri)

    // touch activity
    await apiPatch(bruceApi, `/api/activities/${activityId}`, {
      title: 'Frühstück',
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bruceApi, uri)
    await expectCacheHit(bruceApi, uri)

    const bruceApi2 = await getAuthContext(bruceWayneUser)
    await expectCacheMiss(bruceApi2, uri)
  })

  test('invalidates /camps/{campId}/activities for new activity', async () => {
    const uri = `/api/camps/${grgrCampId}/activities`

    const bipiApi = await getAuthContext(bipiUser)

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // add new activity to camp
    const postRes = await apiPost(bipiApi, '/api/activities', {
      title: 'New_activity',
      category: '/api/categories/1a869b162875',
      scheduleEntries: [
        {
          period: '/periods/76be24bce434',
          start: '2026-05-10T08:00:00+00:00',
          end: '2026-05-10T09:00:00+00:00',
        },
      ],
    })
    const body = await postRes.json()
    const newActivityUri = body._links.self.href

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // delete newly created contentNode
    await apiDelete(bipiApi, newActivityUri)

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)
  })

  test('invalidates /camps/{campId}/activities when adding a scheduleEntry', async () => {
    const uri = `/api/camps/${grgrCampId}/activities`

    const bipiApi = await getAuthContext(bipiUser)

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // add new scheduleEntry
    const postRes = await apiPost(bipiApi, '/api/schedule_entries', {
      activity: '/activities/ffd08c52288c',
      end: '2026-05-11T06:00:00+00:00',
      period: '/periods/76be24bce434',
      start: '2026-05-11T05:00:00+00:00',
    })
    const body = await postRes.json()
    const newScheduleEntryUri = body._links.self.href

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // delete newly created scheduleEntry
    await apiDelete(bipiApi, newScheduleEntryUri)

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)
  })

  test('invalidates /camps/{campId}/activities when patching a progress label', async () => {
    const uri = `/api/camps/${grgrCampId}/activities`
    const progressLabelId = '82547049ea38'

    // bring data into defined state
    const bipiApi = await getAuthContext(bipiUser)
    await apiPatch(bipiApi, `/api/activity_progress_labels/${progressLabelId}`, {
      title: 'Planned',
    })

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // touch progress label
    await apiPatch(bipiApi, `/api/activity_progress_labels/${progressLabelId}`, {
      title: 'Geplant',
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)
  })

  test('invalidates /camps/{campId}/activities when adding an activity responsible', async () => {
    const uri = `/api/camps/${grgrCampId}/activities`

    const bipiApi = await getAuthContext(bipiUser)

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // add new activity responsible
    const postRes = await apiPost(bipiApi, '/api/activity_responsibles', {
      activity: '/activities/ffd08c52288c',
      campCollaboration: '/camp_collaborations/b0bdb7202a9d',
    })
    const body = await postRes.json()
    const newActivityResponsibleUri = body._links.self.href

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // delete newly created activity responsible
    await apiDelete(bipiApi, newActivityResponsibleUri)

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)
  })

  test('invalidates /camps/{campId}/activities when changing the period dates (moveScheduleEntries: true)', async () => {
    const uri = `/api/camps/${grgrCampId}/activities`

    const bipiApi = await getAuthContext(bipiUser)

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // move period start date
    await apiPatch(bipiApi, `/api/periods/${grgrPeriodId}`, {
      start: '2026-05-09',
      end: '2026-05-12',
      moveScheduleEntries: true,
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // move period start date again
    await apiPatch(bipiApi, `/api/periods/${grgrPeriodId}`, {
      start: '2026-05-10',
      end: '2026-05-13',
      moveScheduleEntries: true,
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)
  })

  test('invalidates /camps/{campId}/activities when changing the period dates (moveScheduleEntries: false)', async () => {
    const uri = `/api/camps/${grgrCampId}/activities`

    const bipiApi = await getAuthContext(bipiUser)

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // move period start date
    await apiPatch(bipiApi, `/api/periods/${grgrPeriodId}`, {
      start: '2026-05-09',
      moveScheduleEntries: false,
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // move period start date again
    await apiPatch(bipiApi, `/api/periods/${grgrPeriodId}`, {
      start: '2026-05-10',
      moveScheduleEntries: false,
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)
  })
})
