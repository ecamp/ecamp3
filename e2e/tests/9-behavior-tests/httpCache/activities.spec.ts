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
  'a13fadc97610 a13fadc97610#scheduleEntries a13fadc97610#activityResponsibles ' +
  /* embedded progress labels: */
  'af92782262d7 ' +
  /* embedded schedule entries: */
  /* (the first schedule entry also includes the period id and related custom cache tags) */
  '29c9e9a07d82 7fa4564a5d5d 7fa4564a5d5d^scheduleEntryNumbers 70ca971c992f^dayNumbers ' +
  'f08d69cae18a ' +
  '7e8086d94633 ' +
  /* embedded activitiy responsibles: */
  '06743ccfeedd ' +
  '21bc6661c569 ' +
  /**
   * activity "Skifahren"
   */
  'b29d387cc403 b29d387cc403#scheduleEntries b29d387cc403#activityResponsibles ' +
  /* embedded progress labels: */
  /* embedded schedule entries: */
  'e68f4e47517a ' +
  'f0883e931649 ' +
  'ee85308a97d1 ' +
  'f89a1501dbb6 ' +
  /* embedded activitiy responsibles: */
  'a9a760e36fd8 ' +
  /* collection URI (for detecting addition of new activities) */
  '/api/camps/70ca971c992f/activities'

test.describe('cache test: /camps/{campId}/activities', { tag: '@mature' }, () => {
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

  test('invalidates /activities/{activityId} when a schedule entry of another activity shifts its numbers', async () => {
    const uri = '/api/activities/ffd08c52288c'
    const otherScheduleEntryUri = '/api/schedule_entries/661817f7af15'

    const bipiApi = await getAuthContext(bipiUser)

    // bring data into defined state
    await apiPatch(bipiApi, otherScheduleEntryUri, {
      start: '2036-05-10T18:00:00+00:00',
      end: '2036-05-10T19:00:00+00:00',
    })

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // touch a schedule entry which is not part of the cached response
    await apiPatch(bipiApi, otherScheduleEntryUri, {
      start: '2036-05-10T18:30:00+00:00',
      end: '2036-05-10T19:30:00+00:00',
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // restore
    await apiPatch(bipiApi, otherScheduleEntryUri, {
      start: '2036-05-10T18:00:00+00:00',
      end: '2036-05-10T19:00:00+00:00',
    })
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
          start: '2036-05-10T08:00:00+00:00',
          end: '2036-05-10T09:00:00+00:00',
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
      end: '2036-05-11T06:00:00+00:00',
      period: '/periods/76be24bce434',
      start: '2036-05-11T05:00:00+00:00',
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
      start: '2036-05-09',
      end: '2036-05-12',
      moveScheduleEntries: true,
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // move period start date again
    await apiPatch(bipiApi, `/api/periods/${grgrPeriodId}`, {
      start: '2036-05-10',
      end: '2036-05-13',
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
      start: '2036-05-09',
      moveScheduleEntries: false,
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // move period start date again
    await apiPatch(bipiApi, `/api/periods/${grgrPeriodId}`, {
      start: '2036-05-10',
      moveScheduleEntries: false,
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)
  })
})
