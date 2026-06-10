import { test, expect, type APIRequestContext } from '@playwright/test'
import {
  bipiUser,
  bruceWayneUser,
  castorUser,
  felicitySmoakUser,
  grgrCampId,
  loremIpsumCampId,
} from '@/utils/constants'
import {
  loginAndSetCookie,
  expectCacheHit,
  expectCacheMiss,
  waitForCacheMiss,
  apiGet,
  apiPatch,
  apiPost,
  apiDelete,
  getAuthContext,
} from '@/utils/helpers'
import collectionResponse from '@/test-data/httpCache/categories_collection.json'

const grgrLACategoryId = '1a869b162875'

const collectionXKeys =
  /* campCollaboration for bipiUser */
  'b0bdb7202a9d ' +
  /* Category ES */
  'ebfd46a1c181 ebfd46a1c181#camp ebfd46a1c181#preferredContentTypes ebfd46a1c181#rootContentNode ebfd46a1c181#embeddedPreferredContentTypes ebfd46a1c181#emptyContentNodesForIriGeneration ' +
  /* Category LA */
  '1a869b162875 1a869b162875#camp 1a869b162875#preferredContentTypes 1a869b162875#rootContentNode f17470519474 1a0f84e322c8 3ef17bd1df72 4f0c657fecef a4211c112939 44dcc7493c65 cfccaecd4bad 318e064ea0c9 1a869b162875#embeddedPreferredContentTypes 1a869b162875#emptyContentNodesForIriGeneration ' +
  /* Category LP */
  'dfa531302823 dfa531302823#camp dfa531302823#preferredContentTypes dfa531302823#rootContentNode dfa531302823#embeddedPreferredContentTypes dfa531302823#emptyContentNodesForIriGeneration ' +
  /* Category LS */
  'a023e85227ac a023e85227ac#camp a023e85227ac#preferredContentTypes a023e85227ac#rootContentNode a023e85227ac#embeddedPreferredContentTypes a023e85227ac#emptyContentNodesForIriGeneration ' +
  /* collection URI (for detecting addition of new categories) */
  '/api/camps/3c79b99ab424/categories'

test.describe('cache test: /camps/{campId}/categories', () => {
  test.describe.configure({ mode: 'serial' })

  test('caches /camps/{campId}/categories separately for each login', async () => {
    const uri = `/api/camps/${grgrCampId}/categories`

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

  test('invalidates /camps/{campId}/categories for all users on category patch', async () => {
    const uri = `/api/camps/${loremIpsumCampId}/categories`

    // bring data into defined state
    const bruceApi = await getAuthContext(bruceWayneUser)
    const felicityApi = await getAuthContext(felicitySmoakUser)
    await apiPatch(bruceApi, '/api/categories/c5e1bc565094', {
      name: 'old_name',
    })

    // warm up cache (bruce)
    await apiGet(bruceApi, uri)
    await expectCacheHit(bruceApi, uri)

    // warm up cache (felicity)
    await apiGet(felicityApi, uri)
    await expectCacheHit(felicityApi, uri)

    // touch category (bruce)
    await apiPatch(bruceApi, '/api/categories/c5e1bc565094', {
      name: 'new_name',
    })

    // ensure cache was invalidated
    await waitForCacheMiss(felicityApi, uri)
    await expectCacheHit(felicityApi, uri)

    await expectCacheMiss(bruceApi, uri)
  })

  test('invalidates /camps/{campId}/categories for new category', async () => {
    const uri = `/api/camps/${grgrCampId}/categories`
    const bipiApi = await getAuthContext(bipiUser)

    // warm up cache
    await apiGet(bipiApi, uri)
    await expectCacheHit(bipiApi, uri)

    // add new category to camp
    const postRes = await apiPost(bipiApi, '/api/categories', {
      camp: `/api/camps/${grgrCampId}`,
      short: 'new',
      name: 'new Category',
      color: '#000000',
      numberingStyle: '1',
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

  // eslint-disable-next-line playwright/no-skipped-test
  test.skip('invalidates cached data when user leaves a camp', async ({ browser }) => {
    const castorContext = await browser.newContext()
    const bipiContext = await browser.newContext()
    const castorPage = await castorContext.newPage()
    const bipiPage = await bipiContext.newPage()
    await loginAndSetCookie(castorPage, castorContext, castorUser)
    await loginAndSetCookie(bipiPage, bipiContext, bipiUser)
    const uri = `/api/camps/${grgrCampId}/categories`

    const castorApi = castorContext.request
    const bipiApi = bipiContext.request

    // warm up cache
    await apiGet(castorApi, uri)
    await expectCacheHit(castorApi, uri)

    // deactivate Castor
    await bipiPage.goto(`/camps/${grgrCampId}/GRGR/admin/collaborators`)
    await bipiPage.locator('.v-list-item__title', { hasText: 'Castor' }).click()
    await bipiPage.getByRole('button', { name: 'Deaktivieren' }).first().click()
    await Promise.all([
      bipiPage.waitForResponse(
        (res) =>
          res.url().includes('/api/camp_collaborations/') &&
          res.request().method() === 'PATCH'
      ),
      bipiPage
        .locator('div[role="alert"]')
        .getByRole('button', { name: 'Deaktivieren' })
        .click(),
    ])

    // ensure cache was invalidated
    const notFoundRes = await apiGet(castorApi, uri)
    expect(notFoundRes.status()).toBe(404)

    // delete old emails
    await apiDelete(bipiApi, '/mail/email/all')

    // invite Castor
    await bipiPage.locator('.v-list-item__title', { hasText: 'Castor' }).click()
    await Promise.all([
      bipiPage.waitForResponse(
        (res) =>
          res.url().includes('/api/camp_collaborations/') &&
          res.request().method() === 'PATCH'
      ),
      bipiPage.getByRole('button', { name: 'Erneut einladen' }).click(),
    ])

    // accept invitation as Castor
    const emailRes = await castorApi.get('/mail/email')
    const emails = await emailRes.json()
    const emailHtmlContent = emails[0].html
    await castorPage.setContent(emailHtmlContent)
    const [newPage] = await Promise.all([
      castorContext.waitForEvent('page'),
      castorPage.locator('a', { hasText: 'Einladung beantworten' }).click(),
    ])
    await Promise.all([
      newPage.waitForResponse(
        (res) =>
          res.url().includes('/api/invitations/') && res.request().method() === 'PATCH'
      ),
      newPage
        .getByRole('button', { name: 'Einladung mit aktuellem Account akzeptieren' })
        .click(),
    ])
    await newPage.goto('/camps')
    await expect(newPage.locator('body')).toContainText('GRGR')
  })

  test.describe('invalidates /camps/{campId}/categories', () => {
    // @ts-expect-error we can type this later
    let categoryBefore
    let bipiApi: APIRequestContext
    function preferredContentTypeIrisBefore() {
      // @ts-expect-error we can type this later
      return categoryBefore._embedded.preferredContentTypes.map(
        // @ts-expect-error we can type this later
        (contentType) => contentType._links.self.href
      )
    }

    test.beforeEach(async () => {
      bipiApi = await getAuthContext(bipiUser)
      const res = await apiGet(bipiApi, `/api/categories/${grgrLACategoryId}`)
      categoryBefore = await res.json()
      expect(preferredContentTypeIrisBefore().length).toBeGreaterThan(0)
    })

    test.afterEach(async () => {
      const res = await apiPatch(bipiApi, `/api/categories/${grgrLACategoryId}`, {
        preferredContentTypes: preferredContentTypeIrisBefore(),
        // @ts-expect-error we can type this later
        short: categoryBefore.short,
        // @ts-expect-error we can type this later
        name: categoryBefore.name,
        // @ts-expect-error we can type this later
        color: categoryBefore.color,
        // @ts-expect-error we can type this later
        numberingStyle: categoryBefore.numberingStyle,
      })
      expect(res.status()).toBe(200)
    })

    test('when preferredContentTypes are removed', async () => {
      const uri = `/api/camps/${grgrCampId}/categories`

      // warm up cache
      await apiGet(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)

      // set the preferredContentTypes to empty
      await apiPatch(bipiApi, `/api/categories/${grgrLACategoryId}`, {
        preferredContentTypes: [],
      })

      // ensure cache was invalidated
      await waitForCacheMiss(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)
    })

    test('when preferredContentType is added', async () => {
      const uri = `/api/camps/${grgrCampId}/categories`

      // warm up cache
      await apiGet(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)

      // add new preferredContentType
      await apiPatch(bipiApi, `/api/categories/${grgrLACategoryId}`, {
        preferredContentTypes: [
          ...preferredContentTypeIrisBefore(),
          '/api/content_types/a4211c11211c',
        ],
      })

      // ensure cache was invalidated
      await waitForCacheMiss(bipiApi, uri)
      await expectCacheHit(bipiApi, uri)
    })
  })
})
