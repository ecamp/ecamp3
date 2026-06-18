import { test, expect } from '@playwright/test'
import { bipiUser } from '@/utils/constants'
import { loginAndSetCookie } from '@/utils/helpers'

const CAMP_CREATE_CHUNK = /\/src\/views\/CampCreate\.vue/

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
