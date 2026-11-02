import { test, expect } from '@playwright/test'
import {
  bipiUser,
  bruceWayneUser,
  felicitySmoakUser,
  grgrCampId,
  grgrPeriodId,
} from '@/utils/constants'
import entityResponse from '@/test-data/httpCache/activities_entity.json'
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
  /* activity "Snowboardfahren" + embedded relations */
  'a13fadc97610 a13fadc97610#scheduleEntries a13fadc97610#activityResponsibles ' +
  /* category "Snowboard" */
  'eddb2cd8cee0 eddb2cd8cee0#preferredContentTypes ' +
  /* progressLabel "Rudolf OK" */
  'af92782262d7 ' +
  /* contentNodes */
  '9ace4f6be500 9ace4f6be500#children ' +
  '0f20419761b6 0f20419761b6#children ' +
  '160f007bd04f 160f007bd04f#children ' +
  'aa0738bc61de aa0738bc61de#children ' +
  /* scheduleEntries */
  /* (the first schedule entry also includes the period id) */
  '29c9e9a07d82 7fa4564a5d5d ' +
  'f08d69cae18a ' +
  '7e8086d94633 ' +
  /* activityResponsible */
  '06743ccfeedd ' +
  '21bc6661c569'

test.describe('cache test: /camps/{campId}/activities', { tag: '@mature' }, () => {
  test.describe.configure({ mode: 'serial' })

  test('caches /activities/{activitiyId} separately for each login', async () => {
    const snowboardfahrenActivityId = 'a13fadc97610'
    const uri = `/api/activities/${snowboardfahrenActivityId}`

    const bipiApi = await getAuthContext(bipiUser)

    // first request is a cache miss
    const request = await apiGet(bipiApi, uri)
    const headers = request.headers()
    expect(headers['xkey']).toBe(collectionXKeys)
    expect(headers['x-cache']).toBe('MISS')
    expect(await request.json()).toEqual(entityResponse)

    // second request is a cache hit
    await expectCacheHit(bipiApi, uri)

    // request with a new user is a cache miss
    const bruceApi = await getAuthContext(bruceWayneUser)
    await expectCacheMiss(bruceApi, uri)
  })

  test('invalidates /activities/{activitiyId} for all users on activity patch', async () => {
    const activityId = '3d1e5c91ceb2'
    const uri = `/api/activities/${activityId}`

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

  test('invalidates /activities/{activitiyId} when adding a scheduleEntry', async () => {
    const activityId = 'ffd08c52288c'
    const uri = `/api/activities/${activityId}`

    const bipiApi = await getAuthContext(bipiUser)

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // add new scheduleEntry
    const postRes = await apiPost(bipiApi, '/api/schedule_entries', {
      activity: `/activities/${activityId}`,
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

  test('invalidates /activities/{activitiyId}  when patching a progress label', async () => {
    const activityId = '130f9a87373b'
    const progressLabelId = '82547049ea38'
    const uri = `/api/activities/${activityId}`

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

  test('invalidates /activities/{activitiyId}  when adding an activity responsible', async () => {
    const activityId = 'ffd08c52288c'
    const uri = `/api/activities/${activityId}`

    const bipiApi = await getAuthContext(bipiUser)

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // add new activity responsible
    const postRes = await apiPost(bipiApi, '/api/activity_responsibles', {
      activity: `/activities/${activityId}`,
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

  test('invalidates /activities/{activitiyId} when changing the period dates (moveScheduleEntries: true)', async () => {
    const activityId = '130f9a87373b'
    const uri = `/api/activities/${activityId}`

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

  test('invalidates /activities/{activitiyId} when changing the period dates (moveScheduleEntries: false)', async () => {
    const activityId = '130f9a87373b'
    const uri = `/api/activities/${activityId}`

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

  test('invalidates /activities/{activitiyId}  when adding a contentNode', async () => {
    const activityId = '130f9a87373b'
    const uri = `/api/activities/${activityId}`

    const bipiApi = await getAuthContext(bipiUser)

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // add new contentNode
    const postRes = await apiPost(bipiApi, '/api/content_node/single_texts', {
      contentType: '/content_types/4f0c657fecef',
      parent: '/content_node/column_layouts/0cc124cdd9f6',
      slot: '1',
    })
    const body = await postRes.json()
    const newContentNodeUri = body._links.self.href

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // delete newly created contentNode
    await apiDelete(bipiApi, newContentNodeUri)

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)
  })

  test('invalidates /activities/{activitiyId} when deleting a period with one of its scheduleEntries', async () => {
    const activityId = 'ffd08c52288c'
    const uri = `/api/activities/${activityId}`

    const bipiApi = await getAuthContext(bipiUser)

    // create an additional period with a scheduleEntry of the activity
    const periodRes = await apiPost(bipiApi, '/api/periods', {
      camp: `/camps/${grgrCampId}`,
      description: 'Temporary period',
      start: '2037-05-09',
      end: '2037-05-12',
    })
    const periodUri = (await periodRes.json())._links.self.href
    await apiPost(bipiApi, '/api/schedule_entries', {
      activity: `/activities/${activityId}`,
      period: periodUri,
      start: '2037-05-10T05:00:00+00:00',
      end: '2037-05-10T06:00:00+00:00',
    })

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // delete the period, which removes the scheduleEntry in the database
    await apiDelete(bipiApi, periodUri)

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)
  })
})
