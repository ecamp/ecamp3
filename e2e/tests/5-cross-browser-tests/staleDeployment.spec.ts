import { test, expect } from '@playwright/test'
import { bipiUser } from '@/utils/constants'
import { loginAndSetCookie } from '@/utils/helpers'

const CAMP_CREATE_CHUNK = /.*CampCreate.*/

test('reloads the page when a route chunk is missing after a deploy', async ({
  page,
  request,
  browserName,
}) => {
  //eslint-disable-next-line
  if (browserName === 'webkit') {
    // webkit crashes completely on the production build when an asset fails to load.
    // I also didn't find a way to recover webkit after that.
    //eslint-disable-next-line
    test.skip()
  }
  await loginAndSetCookie(page, request, bipiUser)
  await expect(page.getByTestId('create-camp-button')).toBeVisible()

  await page.evaluate(() => {
    ;(window as unknown as { __noReload: boolean }).__noReload = true
  })

  let chunkRequestCount = 0
  await page.route(CAMP_CREATE_CHUNK, async (route) => {
    if (!route.request().url().endsWith('.js')) {
      await route.continue()
      return
    }
    chunkRequestCount += 1
    if (chunkRequestCount === 1) {
      await route.abort('failed')
    } else {
      await route.continue()
    }
  })

  await page.getByTestId('create-camp-button').click()

  await expect(page.locator('[data-testid="create-camp-title-input"] input')).toBeVisible(
    {
      timeout: 30000,
    }
  )
  await expect(page).toHaveURL(/\/camps\/create$/)

  const markerSurvived = await page.evaluate(
    () => (window as unknown as { __noReload?: boolean }).__noReload === true
  )
  expect(markerSurvived).toBe(false)
  expect(chunkRequestCount).toBeGreaterThanOrEqual(2)
})

const FEATURE_CHUNK_PATH = '/src/components/activity/ContentNode.vue'

test(
  'shows the update popup instead of reloading when an in-page feature chunk is missing',
  { tag: '@mature' },
  async ({ page, request, browserName }) => {
    //eslint-disable-next-line
    if (browserName === 'webkit') {
      // webkit just silently fails when a chunk cannot be loaded without a route change.
      //eslint-disable-next-line
      test.skip()
    }
    await loginAndSetCookie(page, request, bipiUser)
    await expect(page.getByTestId('create-camp-button')).toBeVisible()
    const urlBefore = page.url()

    await page.evaluate(() => {
      ;(window as unknown as { __noReload: boolean }).__noReload = true
    })

    await page.route(FEATURE_CHUNK_PATH, (route) =>
      route.fulfill({ status: 404, contentType: 'text/plain', body: 'gone' })
    )

    await page.evaluate((path) => {
      void import(/* @vite-ignore */ path)
    }, FEATURE_CHUNK_PATH)

    const dialog = page.getByTestId('new-version-dialog')
    await expect(dialog).toBeVisible({ timeout: 30000 })
    await expect(page.getByTestId('new-version-update')).toBeVisible()
    await expect(page.getByTestId('new-version-continue')).toBeVisible()

    const markerSurvived = await page.evaluate(
      () => (window as unknown as { __noReload?: boolean }).__noReload === true
    )
    expect(markerSurvived).toBe(true)
    expect(page.url()).toBe(urlBefore)

    await page.getByTestId('new-version-continue').click()
    await expect(dialog).toBeHidden()
    expect(page.url()).toBe(urlBefore)
    const stillNoReload = await page.evaluate(
      () => (window as unknown as { __noReload?: boolean }).__noReload === true
    )
    expect(stillNoReload).toBe(true)
  }
)
