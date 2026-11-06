import { test, expect } from '@playwright/test'
import {
  bipiUser,
  castorUser,
  grgrPeriodId,
  harryMainPeriodId,
  harrySecondPeriodId,
  bruceWayneUser,
} from '@/utils/constants'
import {
  expectCacheHit,
  waitForCacheMiss,
  apiGet,
  purgeCache,
  apiPatch,
  apiPost,
  apiDelete,
  getAuthContext,
  getCacheInfo,
} from '@/utils/helpers'
import collectionResponse from '@/test-data/httpCache/days_collection.json'

const addDays = (date: string, days: number) => {
  const result = new Date(date)
  result.setUTCDate(result.getUTCDate() + days)
  return result.toISOString().substring(0, 10)
}

const collectionXKeys =
  /* days + links + dayResponsibles  */
  /* the first day also includes the period id 76be24bce434 and the camp's day numbers key */
  '4b90ff5b42c0 76be24bce434 3c79b99ab424^dayNumbers 4b90ff5b42c0#period 4b90ff5b42c0#scheduleEntries cf442e7381e9 cf442e7381e9#day cf442e7381e9#campCollaboration 4b90ff5b42c0#dayResponsibles ' +
  'ac92acd57c49 ac92acd57c49#period ac92acd57c49#scheduleEntries c58a08b98726 c58a08b98726#day c58a08b98726#campCollaboration ac92acd57c49#dayResponsibles ' +
  '1447f15f1e12 1447f15f1e12#period 1447f15f1e12#scheduleEntries db401ffc07e6 db401ffc07e6#day db401ffc07e6#campCollaboration 1447f15f1e12#dayResponsibles ' +
  '963c3c0639dd 963c3c0639dd#period 963c3c0639dd#scheduleEntries 981300f9ef5c 981300f9ef5c#day 981300f9ef5c#campCollaboration 963c3c0639dd#dayResponsibles ' +
  /* collection URI (for detecting addition of new days) */
  '/api/periods/76be24bce434/days'

test.describe('cache test: /periods/{periodId}/days', { tag: '@mature' }, () => {
  test.describe.configure({ mode: 'serial' })

  test.beforeEach(purgeCache)

  test('shares cache for /periods/{periodId}/days between users with camp access', async () => {
    const uri = `/api/periods/${grgrPeriodId}/days`

    const bipiApi = await getAuthContext(bipiUser)

    // first request is a cache miss
    const res1 = await apiGet(bipiApi, uri)
    const headers = res1.headers()
    expect(headers['xkey']).toBe(collectionXKeys)
    expect(headers['x-cache']).toBe('MISS')
    expect(await res1.json()).toEqual(collectionResponse)

    // second request is a cache hit
    await expectCacheHit(bipiApi, uri)

    // request with another user with access is a cache hit
    const castorApi = await getAuthContext(castorUser)
    await expectCacheHit(castorApi, uri)

    // a user without access to the camp does not get the shared response
    const outsiderApi = await getAuthContext(bruceWayneUser)
    expect(await getCacheInfo(outsiderApi, uri)).toMatchObject({
      status: 404,
      cache: 'PASS',
    })
  })

  test('invalidates /periods/{periodId}/days when changing the period dates', async () => {
    const uri = `/api/periods/${grgrPeriodId}/days`

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

  test('invalidates /periods/{periodId}/days of later periods when changing the dates of an earlier period', async () => {
    const uri = `/api/periods/${harrySecondPeriodId}/days`
    const mainPeriodUri = `/api/periods/${harryMainPeriodId}`

    const bipiApi = await getAuthContext(bipiUser)
    const mainPeriod = await (await apiGet(bipiApi, mainPeriodUri)).json()

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // lengthen the earlier period, which shifts the day numbers of the later period
    await apiPatch(bipiApi, mainPeriodUri, { end: addDays(mainPeriod.end, 1) })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // restore
    await apiPatch(bipiApi, mainPeriodUri, { end: mainPeriod.end })
    await waitForCacheMiss(bipiApi, uri)
  })

  test('invalidates /periods/{periodId}/days when adding a day responsible', async () => {
    const uri = `/api/periods/${grgrPeriodId}/days`

    const bipiApi = await getAuthContext(bipiUser)

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // add new day responsible
    const postRes = await apiPost(bipiApi, '/api/day_responsibles', {
      day: '/days/4b90ff5b42c0',
      campCollaboration: '/camp_collaborations/c88fd78c90ea',
    })
    const body = await postRes.json()
    const newDayResponsibleUri = body._links.self.href

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // delete newly created day responsible
    await apiDelete(bipiApi, newDayResponsibleUri)

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)
  })
})
