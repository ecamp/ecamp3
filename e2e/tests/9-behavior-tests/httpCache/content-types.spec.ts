import { test, expect } from '@playwright/test'
import collectionResponse from '@/test-data/httpCache/content_types_collection.json'
import itemResponse from '@/test-data/httpCache/content_types_entity.json'
import { bipiUser, castorUser } from '@/utils/constants'
import { expectCacheHit, expectCacheMiss, apiGet, getAuthContext } from '@/utils/helpers'

const collectionXKeys =
  'a4211c11211c f17470519474 1a0f84e322c8 c462edd869f3 5e2028c55ee4 3ef17bd1df72 4f0c657fecef a4211c112939 44dcc7493c65 cfccaecd4bad 318e064ea0c9 /api/content_types'

test.describe('cache test: /content-types', () => {
  test.describe.configure({ mode: 'serial' })

  test('caches collection separately for each login', async () => {
    const uri = '/api/content_types'

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

  test('caches item', async () => {
    const contentTypeId = '318e064ea0c9'
    const uri = `/api/content_types/${contentTypeId}`

    const bipiApi = await getAuthContext(bipiUser)

    // first request is a cache miss
    const res1 = await apiGet(bipiApi, uri)
    const headers = res1.headers()
    expect(headers['xkey']).toBe(contentTypeId)
    expect(headers['x-cache']).toBe('MISS')
    expect(await res1.json()).toEqual(itemResponse)

    // second request is a cache hit
    await expectCacheHit(bipiApi, uri)
  })
})
