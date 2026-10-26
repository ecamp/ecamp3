import { test, expect, request } from '@playwright/test'
import {
  bipiUser,
  bruceWayneUser,
  grgrCampId,
  jsKursPrototypeCampId,
} from '@/utils/constants'
import { apiCheckAccess, getAuthContext } from '@/utils/helpers'

const campIri = (campId: string) => `/api/camps/${campId}`

/* campCollaboration of bipiUser in the grgr camp */
const bipiGrgrCollaborationId = 'b0bdb7202a9d'

const maxAge = (cacheControl: string) =>
  Number(/s-maxage=(\d+)/.exec(cacheControl)?.[1] ?? Number.NaN)

/**
 * The access check which the cache makes before it serves a cached response to a user,
 * requested directly from the API.
 */
test.describe('check access: /api/authorization/check_access', { tag: '@mature' }, () => {
  test('grants access to a collaborator of the camp', async () => {
    const bipiApi = await getAuthContext(bipiUser)

    const response = await apiCheckAccess(bipiApi, campIri(grgrCampId))

    expect(response.status()).toBe(204)
  })

  test('grants access only for the lifetime of the login and tags it with the collaboration', async () => {
    const bipiApi = await getAuthContext(bipiUser)

    const headers = (await apiCheckAccess(bipiApi, campIri(grgrCampId))).headers()

    expect(headers['cache-control']).toContain('public')
    expect(maxAge(headers['cache-control'])).toBeGreaterThan(0)
    expect(maxAge(headers['cache-control'])).toBeLessThanOrEqual(43200)
    // purging the collaboration revokes the access
    expect(headers['xkey']).toContain(bipiGrgrCollaborationId)
  })

  test('grants access to everyone for a public camp and tags it with the camp', async () => {
    const bipiApi = await getAuthContext(bipiUser)

    const response = await apiCheckAccess(bipiApi, campIri(jsKursPrototypeCampId))

    expect(response.status()).toBe(204)
    // purging the camp revokes the access, e.g. when it stops being public
    expect(response.headers()['xkey']).toContain(jsKursPrototypeCampId)
  })

  test('denies access to a user who is not a collaborator of the camp', async () => {
    const bruceApi = await getAuthContext(bruceWayneUser)

    const response = await apiCheckAccess(bruceApi, campIri(grgrCampId))

    expect(response.status()).toBe(403)
  })

  test('does not allow to cache a denial', async () => {
    const bruceApi = await getAuthContext(bruceWayneUser)

    const headers = (await apiCheckAccess(bruceApi, campIri(grgrCampId))).headers()

    expect(headers['cache-control']).toContain('no-store')
    expect(headers['cache-control']).not.toContain('public')
    expect(headers['xkey']).toBeUndefined()
  })

  test('denies access to something which is no readable resource', async () => {
    const bipiApi = await getAuthContext(bipiUser)

    const noResource = await apiCheckAccess(bipiApi, 'public')
    const unknownResource = await apiCheckAccess(bipiApi, campIri('000000000000'))

    for (const response of [noResource, unknownResource]) {
      expect(response.status()).toBe(403)
    }
  })

  test('requires the iri', async () => {
    const bipiApi = await getAuthContext(bipiUser)

    const response = await apiCheckAccess(bipiApi)

    expect(response.status()).toBe(400)
  })

  test('requires a login', async () => {
    const anonymous = await request.newContext()

    const response = await apiCheckAccess(anonymous, campIri(grgrCampId))

    expect(response.status()).toBe(401)
  })
})
