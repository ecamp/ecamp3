import { test, expect, request, type APIRequestContext } from '@playwright/test'
import { bipiUser, bruceWayneUser, castorUser, grgrCampId } from '@/utils/constants'
import {
  API_ROOT_URL_CACHED,
  apiDelete,
  apiGet,
  apiCheckAccess,
  apiPatch,
  apiPost,
  getAuthContext,
  getCacheInfo,
  purgeCache,
} from '@/utils/helpers'

/**
 * Cached responses are shared between users. The cache serves a shared response to a user only after
 * the API granted that user access to the resource the response delegates its security to (the camp).
 * The access check (preflight) is only visible on a cache hit, and it is cached per user and camp.
 */
test.describe('cache test: shared responses', { tag: '@mature' }, () => {
  test.describe.configure({ mode: 'serial' })

  test.beforeEach(purgeCache)

  const uri = `/api/camps/${grgrCampId}/categories`

  test('shares a response between users with access, checking access once per user', async () => {
    const bipiApi = await getAuthContext(bipiUser)
    const castorApi = await getAuthContext(castorUser)

    // a miss is authorized by the API itself, no access check
    expect(await getCacheInfo(bipiApi, uri)).toEqual({
      status: 200,
      cache: 'MISS',
      preflight: undefined,
    })

    // a hit for another user checks the access of that user once
    expect(await getCacheInfo(castorApi, uri)).toEqual({
      status: 200,
      cache: 'HIT',
      preflight: 'MISS',
    })
    expect(await getCacheInfo(castorApi, uri)).toEqual({
      status: 200,
      cache: 'HIT',
      preflight: 'HIT',
    })

    // the first user has to be checked too, since the response was not fetched for a cache hit
    expect(await getCacheInfo(bipiApi, uri)).toEqual({
      status: 200,
      cache: 'HIT',
      preflight: 'MISS',
    })
    expect(await getCacheInfo(bipiApi, uri)).toEqual({
      status: 200,
      cache: 'HIT',
      preflight: 'HIT',
    })
  })

  test('does not serve a shared response to users without access', async () => {
    const bipiApi = await getAuthContext(bipiUser)
    const bruceApi = await getAuthContext(bruceWayneUser)
    const anonymous = await request.newContext()
    await apiGet(bipiApi, uri)

    // the denial is not cached: access is checked on every request
    for (let i = 0; i < 2; i++) {
      expect(await getCacheInfo(bruceApi, uri)).toEqual({
        status: 404,
        cache: 'PASS',
        preflight: 'MISS',
      })
    }

    expect(await getCacheInfo(anonymous, uri)).toMatchObject({
      status: 401,
      cache: 'PASS',
    })
  })

  test('does not share responses which do not delegate their security', async () => {
    // comments can only be read by collaborators, not in public camps
    const commentsUri = '/api/activities/a13fadc97610/comments'
    const bruceApi = await getAuthContext(bruceWayneUser)
    const bipiApi = await getAuthContext(bipiUser)

    for (const api of [bruceApi, bipiApi, bruceApi, bipiApi]) {
      const info = await getCacheInfo(api, commentsUri)
      expect(info.status).toBe(200)
      expect(info.cache).not.toBe('HIT')
      expect(info.preflight).toBeUndefined()
    }
  })

  test('shares public responses without checking access', async () => {
    const publicUri = '/api/content_types'
    const bipiApi = await getAuthContext(bipiUser)
    const bruceApi = await getAuthContext(bruceWayneUser)
    await apiGet(bipiApi, publicUri)

    expect(await getCacheInfo(bruceApi, publicUri)).toEqual({
      status: 200,
      cache: 'HIT',
      preflight: undefined,
    })
  })

  test('does not cache the access check when a client requests it through the cache', async () => {
    const bipiApi = await getAuthContext(bipiUser)
    const bruceApi = await getAuthContext(bruceWayneUser)
    const checkAccess = (api: APIRequestContext) =>
      api.get(`${API_ROOT_URL_CACHED}/api/authorization/check_access`, {
        params: { iri: `/api/camps/${grgrCampId}` },
        headers: { Accept: 'application/hal+json' },
      })

    // everybody gets the answer of the API for their own login, nothing is taken from the cache
    for (const [api, status] of [
      [bipiApi, 204],
      [bruceApi, 403],
      [bipiApi, 204],
    ] as const) {
      const response = await checkAccess(api)
      expect(response.status()).toBe(status)
      expect(response.headers()['x-cache']).not.toBe('HIT')
    }
    expect(await getCacheInfo(bruceApi, uri)).toMatchObject({
      status: 404,
      cache: 'PASS',
    })
  })

  test.describe('revokes access to shared responses', () => {
    test('when a camp stops being public', async () => {
      const bipiApi = await getAuthContext(bipiUser)
      const bruceApi = await getAuthContext(bruceWayneUser)

      // bipi creates a camp of its own, which bruce has no access to
      const createResponse = await apiPost(bipiApi, '/api/camps', {
        title: 'Cache e2e test camp',
        motto: '',
        addressName: '',
        addressStreet: '',
        addressZipcode: '',
        addressCity: '',
        periods: [{ description: 'Main', start: '2031-01-01', end: '2031-01-03' }],
      })
      expect(createResponse.status()).toBe(201)
      const campId = (await createResponse.json()).id
      const campUri = `/api/camps/${campId}`
      const activitiesUri = `${campUri}/activities`

      try {
        expect(await getCacheInfo(bipiApi, activitiesUri)).toMatchObject({
          status: 200,
          cache: 'MISS',
        })
        expect(await getCacheInfo(bruceApi, activitiesUri)).toMatchObject({ status: 404 })
        expect((await apiCheckAccess(bruceApi, campUri)).status()).toBe(403)

        // making the camp public gives bruce access
        expect((await apiPatch(bipiApi, campUri, { isShared: true })).ok()).toBe(true)
        await apiGet(bipiApi, activitiesUri)
        expect(await getCacheInfo(bruceApi, activitiesUri)).toEqual({
          status: 200,
          cache: 'HIT',
          preflight: 'MISS',
        })
        expect(await getCacheInfo(bruceApi, activitiesUri)).toEqual({
          status: 200,
          cache: 'HIT',
          preflight: 'HIT',
        })
        const granted = await apiCheckAccess(bruceApi, campUri)
        expect(granted.status()).toBe(204)
        expect(granted.headers()['xkey']).toContain(campId)

        // making it private again revokes it immediately, although bruce's access check was cached
        expect((await apiPatch(bipiApi, campUri, { isShared: false })).ok()).toBe(true)
        expect(await getCacheInfo(bruceApi, activitiesUri)).toMatchObject({
          status: 404,
          cache: 'PASS',
        })
        expect((await apiCheckAccess(bruceApi, campUri)).status()).toBe(403)
        // the collaborator is not affected
        expect(await getCacheInfo(bipiApi, activitiesUri)).toMatchObject({ status: 200 })
      } finally {
        await apiDelete(bipiApi, campUri)
      }
    })
  })
})
