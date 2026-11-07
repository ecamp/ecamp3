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
  /* the first scheduleEntry also includes the period id 7fa4564a5d5d and related custom cache tags */
  'e68f4e47517a 7fa4564a5d5d 7fa4564a5d5d^scheduleEntryNumbers 70ca971c992f^dayNumbers ' +
  'f0883e931649 ' +
  '29c9e9a07d82 ' +
  'ee85308a97d1 ' +
  'f08d69cae18a ' +
  '7e8086d94633 ' +
  'f89a1501dbb6 ' +
  /* collection URI (for detecting addition of new schedule entries) */
  '/api/periods/7fa4564a5d5d/schedule_entries'

test.describe(
  'cache test: /periods/{periodId}/scheduleEntries',
  { tag: '@mature' },
  () => {
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
        start: '2036-05-10T16:00:00+00:00',
      })

      // warm up cache
      await apiGet(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)

      const castorApi = await getAuthContext(castorUser)
      await apiGet(castorApi, uri)
      await expectCacheHit(castorApi, uri)

      // touch scheduleEntry
      await apiPatch(castorApi, `/api/schedule_entries/${scheduleEntryId}`, {
        start: '2036-05-10T17:00:00+00:00',
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
        start: '2036-05-10T10:00:00+00:00',
        end: '2036-05-10T11:00:00+00:00',
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
        start: '2036-08-09T15:00:00+00:00',
        end: '2036-08-09T17:00:00+00:00',
        period: `/periods/${harrySecondPeriodId}`,
      })

      // ensure cache was invalidated
      await waitForCacheMiss(bipiApi, uri1)
      await waitForCacheMiss(bipiApi, uri2)
      await expectCacheHit(bipiApi, uri1)
      await expectCacheHit(bipiApi, uri2)

      // move scheduleEntry back
      await apiPatch(bipiApi, `/api/schedule_entries/${scheduleEntryId}`, {
        start: '2036-07-20T15:00:00+00:00',
        end: '2036-07-20T17:00:00+00:00',
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
        start: '2036-05-09',
        end: '2036-05-12',
        moveScheduleEntries: true,
      })

      // ensure cache was invalidated
      await waitForCacheMiss(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)

      // move period start date
      await apiPatch(bipiApi, `/api/periods/${grgrPeriodId}`, {
        start: '2036-05-10',
        end: '2036-05-13',
        moveScheduleEntries: true,
      })

      // ensure cache was invalidated
      await waitForCacheMiss(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)
    })

    test('invalidates /periods/{periodId}/schedule_entries when changing the numbering style of a category', async () => {
      const uri = `/api/periods/${grgrPeriodId}/schedule_entries`
      const categoryUri = '/api/categories/a023e85227ac'

      const bipiApi = await getAuthContext(bipiUser)

      // bring data into defined state
      await apiPatch(bipiApi, categoryUri, { numberingStyle: '1' })

      // warm up cache
      await apiGet(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)

      // the category is not part of the cached response
      await apiPatch(bipiApi, categoryUri, { numberingStyle: 'A' })

      // ensure cache was invalidated
      await waitForCacheMiss(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)

      // restore
      await apiPatch(bipiApi, categoryUri, { numberingStyle: '1' })
    })

    test('invalidates /periods/{periodId}/schedule_entries when changing the category of an activity', async () => {
      const uri = `/api/periods/${grgrPeriodId}/schedule_entries`
      const activityUri = '/api/activities/ffd08c52288c'

      const bipiApi = await getAuthContext(bipiUser)

      // bring data into defined state
      await apiPatch(bipiApi, activityUri, { category: '/api/categories/a023e85227ac' })

      // warm up cache
      await apiGet(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)

      // the category is not part of the cached response
      await apiPatch(bipiApi, activityUri, { category: '/api/categories/1a869b162875' })

      // ensure cache was invalidated
      await waitForCacheMiss(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)

      // restore
      await apiPatch(bipiApi, activityUri, { category: '/api/categories/a023e85227ac' })
    })

    test('invalidates /periods/{periodId}/schedule_entries of later periods when changing the dates of an earlier period', async () => {
      const uri = `/api/periods/${harrySecondPeriodId}/schedule_entries`
      const mainPeriodUri = `/api/periods/${harryMainPeriodId}`

      const bipiApi = await getAuthContext(bipiUser)
      const mainPeriod = await (await apiGet(bipiApi, mainPeriodUri)).json()

      // warm up cache
      await apiGet(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)

      // lengthen the earlier period, which shifts the day numbers of the later period
      const end = new Date(mainPeriod.end)
      end.setUTCDate(end.getUTCDate() + 1)
      await apiPatch(bipiApi, mainPeriodUri, {
        end: end.toISOString().substring(0, 10),
      })

      // ensure cache was invalidated
      await waitForCacheMiss(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)

      // restore
      await apiPatch(bipiApi, mainPeriodUri, { end: mainPeriod.end })
    })
  }
)
