import { test, expect } from '@playwright/test'
import { bipiUser, bruceWayneUser, grgrPeriodId } from '@/utils/constants'
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
import collectionResponse from '@/test-data/httpCache/days_collection.json'

const collectionXKeys =
  /* campCollaboration for bipiUser */
  'b0bdb7202a9d ' +
  /* days + links + dayResponsibles  */
  /* the first day also includes the period id 76be24bce434 */
  '4b90ff5b42c0 76be24bce434 4b90ff5b42c0#period 4b90ff5b42c0#scheduleEntries cf442e7381e9 cf442e7381e9#day cf442e7381e9#campCollaboration 4b90ff5b42c0#dayResponsibles ' +
  'ac92acd57c49 ac92acd57c49#period ac92acd57c49#scheduleEntries c58a08b98726 c58a08b98726#day c58a08b98726#campCollaboration ac92acd57c49#dayResponsibles ' +
  '1447f15f1e12 1447f15f1e12#period 1447f15f1e12#scheduleEntries db401ffc07e6 db401ffc07e6#day db401ffc07e6#campCollaboration 1447f15f1e12#dayResponsibles ' +
  '963c3c0639dd 963c3c0639dd#period 963c3c0639dd#scheduleEntries 981300f9ef5c 981300f9ef5c#day 981300f9ef5c#campCollaboration 963c3c0639dd#dayResponsibles ' +
  /* collection URI (for detecting addition of new days) */
  '/api/periods/76be24bce434/days'

test.describe('cache test: /periods/{periodId}/days', () => {
  test.describe.configure({ mode: 'serial' })

  test('caches /periods/{periodId}/days separately for each login', async () => {
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

    // request with a new user is a cache miss
    const bruceApi = await getAuthContext(bruceWayneUser)
    await expectCacheMiss(bruceApi, uri)
  })

  test('invalidates /periods/{periodId}/days when changing the period dates', async () => {
    const uri = `/api/periods/${grgrPeriodId}/days`

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
