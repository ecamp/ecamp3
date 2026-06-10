import { test, expect } from '@playwright/test'
import { bipiUser, castorUser, basiskursCampId } from '@/utils/constants'
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
import collectionResponse from '@/test-data/httpCache/checklists_collection.json'

const collectionXKeys =
  /* campCollaboration for bipiUser */
  '146c0608237f ' +
  /* checklist entry */
  'ebbd0c61eb85 ebbd0c61eb85#camp ' +
  /* collection URI (for detecting addition of new checklists) */
  '/api/camps/5d28f99890bc/checklists'

test.describe('cache test: /camps/checklists', () => {
  test.describe.configure({ mode: 'serial' })

  test('caches /camp/{campId}/checklists separately for each login', async () => {
    const uri = `/api/camps/${basiskursCampId}/checklists`

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
    const castorApi = await getAuthContext(castorUser)
    await expectCacheMiss(castorApi, uri)
  })

  test('invalidates /camp/{campId}/checklists on checklist patch', async () => {
    const uri = `/api/camps/${basiskursCampId}/checklists`

    // bring data into defined state
    const bipiApi = await getAuthContext(bipiUser)
    await apiPatch(bipiApi, '/api/checklists/ebbd0c61eb85', {
      name: 'Training targets',
    })

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // touch checklist
    await apiPatch(bipiApi, '/api/checklists/ebbd0c61eb85', {
      name: 'Ausbildungsziele',
    })

    // ensure cache was invalidated
    await waitForCacheMiss(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)
  })

  test.describe('invalidates /camp/{campId}/checklists for new checklist', () => {
    test.describe.configure({ retries: 3 })

    test('try', async () => {
      const uri = `/api/camps/${basiskursCampId}/checklists`

      const bipiApi = await getAuthContext(bipiUser)

      // warm up cache
      await apiGet(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)

      // add new checklist to camp
      const postRes = await apiPost(bipiApi, '/api/checklists', {
        camp: `/api/camps/${basiskursCampId}`,
        name: 'new_checklist',
      })
      const body = await postRes.json()
      const newChecklistUri = body._links.self.href

      // ensure cache was invalidated
      await waitForCacheMiss(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)

      // delete newly created contentNode
      await apiDelete(bipiApi, newChecklistUri)

      // ensure cache was invalidated
      await waitForCacheMiss(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)
    })
  })
})
