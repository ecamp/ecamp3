import { test, expect } from '@playwright/test'
import {
  bipiUser,
  bruceWayneUser,
  castorUser,
  skilagerPeriodId,
  grgrPeriodId,
  harryMainPeriodId,
  harrySecondPeriodId,
} from '@/utils/constants'
import {
  expectCacheHit,
  expectCacheMiss,
  waitForCacheMiss,
  apiGet,
  apiPatch,
  apiPost,
  apiDelete,
  getAuthContext,
} from '@/utils/helpers'
import collectionResponse from '@/test-data/httpCache/schedule_entries_collection.json'

const collectionXKeys =
  /* campCollaboration for bipiUser */
  '10d8f02ce5b4 ' +
  /* scheduleEntries + links */
  /* the first scheduleEntry also includes the period id 7fa4564a5d5d */
  'e68f4e47517a 7fa4564a5d5d e68f4e47517a#period e68f4e47517a#activity e68f4e47517a#day ' +
  'f0883e931649 f0883e931649#period f0883e931649#activity f0883e931649#day ' +
  '29c9e9a07d82 29c9e9a07d82#period 29c9e9a07d82#activity 29c9e9a07d82#day ' +
  'ee85308a97d1 ee85308a97d1#period ee85308a97d1#activity ee85308a97d1#day ' +
  'f08d69cae18a f08d69cae18a#period f08d69cae18a#activity f08d69cae18a#day ' +
  '7e8086d94633 7e8086d94633#period 7e8086d94633#activity 7e8086d94633#day ' +
  'f89a1501dbb6 f89a1501dbb6#period f89a1501dbb6#activity f89a1501dbb6#day ' +
  /* collection URI (for detecting addition of new schedule entries) */
  '/api/periods/7fa4564a5d5d/schedule_entries'

test.describe('cache test: /periods/{periodId}/scheduleEntries', () => {
  test.describe.configure({ mode: 'serial' })

  test('caches /periods/{periodId}/schedule_entries separately for each login', async () => {
    const uri = `/api/periods/${skilagerPeriodId}/schedule_entries`

    const bipiApi = await getAuthContext(bipiUser)

    // first request is a cache miss
    const res1 = await apiGet(bipiApi, uri)
    const headers = res1.headers()
    expect(headers['xkey']).toBe(collectionXKeys)
    expect(headers['x-cache']).toBe('MISS')
    expect(await res1.json()).toEqual(collectionResponse)

    // second request is a cache hit
    await expectCacheHit(bipiApi, uri)

    // request with a new user is a cache miss
    const bruceApi = await getAuthContext(bruceWayneUser)
    await expectCacheMiss(bruceApi, uri)
  })

  test('invalidates /periods/{periodId}/schedule_entries for all users on scheduleEntry patch', async () => {
    const uri = `/api/periods/${grgrPeriodId}/schedule_entries`
    const scheduleEntryId = '12f34c89ce11'

    // bring data into defined state
    const bipiApi = await getAuthContext(bipiUser)
    await apiPatch(bipiApi, `/api/schedule_entries/${scheduleEntryId}`, {
      start: '2026-05-10T16:00:00+00:00',
    })

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    const castorApi = await getAuthContext(castorUser)
    await apiGet(castorApi, uri)
    await expectCacheHit(castorApi, uri)

    // touch scheduleEntry
    await apiPatch(castorApi, `/api/schedule_entries/${scheduleEntryId}`, {
      start: '2026-05-10T17:00:00+00:00',
    })

    // ensure cache was invalidated
    await waitForCacheMiss(castorApi, uri)
    await expectCacheHit(castorApi, uri)

    await expectCacheMiss(bipiApi, uri)
  })

  test('invalidates /periods/{periodId}/schedule_entries for new scheduleEntry', async () => {
    const uri = `/api/periods/${grgrPeriodId}/schedule_entries`

    const bipiApi = await getAuthContext(bipiUser)

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // add new scheduleEntry to period
    const postRes = await apiPost(bipiApi, '/api/schedule_entries', {
      start: '2026-05-10T10:00:00+00:00',
      end: '2026-05-10T11:00:00+00:00',
      period: `/api/periods/${grgrPeriodId}`,
      activity: `/api/activities/ffd08c52288c`,
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

  test('invalidates /periods/{periodId}/schedule_entries when moving a schedule entry to another period', async () => {
    const uri1 = `/api/periods/${harryMainPeriodId}/schedule_entries`
    const uri2 = `/api/periods/${harrySecondPeriodId}/schedule_entries`
    const scheduleEntryId = '9a4173c9bb73'

    const bipiApi = await getAuthContext(bipiUser)

    // warm up cache
    await apiGet(bipiApi, uri1)
    await apiGet(bipiApi, uri2)
    await expectCacheHit(bipiApi, uri1)
    await expectCacheHit(bipiApi, uri2)

    // move scheduleEntry to 2nd period
    await apiPatch(bipiApi, `/api/schedule_entries/${scheduleEntryId}`, {
      start: '2026-08-09T15:00:00+00:00',
      end: '2026-08-09T17:00:00+00:00',
      period: `/periods/${harrySecondPeriodId}`,
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri1)
    await waitForCacheMiss(bipiApi, uri2)
    await expectCacheHit(bipiApi, uri1)
    await expectCacheHit(bipiApi, uri2)

    // move scheduleEntry back
    await apiPatch(bipiApi, `/api/schedule_entries/${scheduleEntryId}`, {
      start: '2026-07-20T15:00:00+00:00',
      end: '2026-07-20T17:00:00+00:00',
      period: `/periods/${harryMainPeriodId}`,
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri1)
    await waitForCacheMiss(bipiApi, uri2)
    await expectCacheHit(bipiApi, uri1)
    await expectCacheHit(bipiApi, uri2)
  })

  test('invalidates /periods/{periodId}/schedule_entries when changing the period dates', async () => {
    const uri = `/api/periods/${grgrPeriodId}/schedule_entries`

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

    // move period start date
    await apiPatch(bipiApi, `/api/periods/${grgrPeriodId}`, {
      start: '2026-05-10',
      end: '2026-05-13',
      moveScheduleEntries: true,
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)
  })
})
