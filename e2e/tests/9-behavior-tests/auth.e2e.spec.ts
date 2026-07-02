import { type BrowserContext, expect, type Page, test } from '@playwright/test'
import { loginAndSetCookie } from '@/utils/helpers'

const COOKIE_PREFIX = 'localhost_'
const S_COOKIE = `${COOKIE_PREFIX}jwt_s`

test.describe('auth token handling', () => {
  test('expired jwt is refreshed and the protected page loads', async ({
    page,
    context,
  }) => {
    await loginAndSetCookie(page, context, 'test@example.com')
    const campRows = page.locator('.v-list-item[role="link"]')
    await expect.poll(async () => campRows.count()).toBeGreaterThan(3)

    await context.clearCookies({ name: S_COOKIE })

    await page.goto('/camps')
    await expect(page).toHaveURL(/\/camps/, { timeout: 15000 })
    await expect.poll(async () => campRows.count()).toBeGreaterThan(3)
  })

  test('redirects to login when the refresh fails', async ({ page }) => {
    await page.route(/\/token\/refresh$/, (route) =>
      route.fulfill({ status: 401, body: '' })
    )

    await page.goto('/camps')
    await expect(page).toHaveURL(/\/login/)
  })

  test('skips refresh and redirects to login when refresh token is known expired', async ({
    page,
  }) => {
    await page.addInitScript(() => {
      localStorage.setItem('refreshTokenExpiresAt', '1')
    })

    await page.goto('/camps')
    await expect(page).toHaveURL(/\/login/)
  })

  test('mid-session 401 triggers transparent refresh and page stays loaded', async ({
    page,
    context,
  }) => {
    await loginAndSetCookie(page, context, 'test@example.com')

    // Make the first camps request fail with 401; no anchor so query params are ignored
    let failed = false
    await page.route(/\/api\/camps/, async (route) => {
      if (!failed) {
        failed = true
        await route.fulfill({ status: 401, body: '' })
      } else {
        await route.continue()
      }
    })

    await page.reload()
    await expect(page).toHaveURL(/\/camps/, { timeout: 15000 })
  })

  test('token refreshed in one tab keeps another tab authenticated', async ({
    browser,
  }) => {
    const context: BrowserContext = await browser.newContext()

    try {
      const tabA: Page = await context.newPage()
      const tabB: Page = await context.newPage()

      await loginAndSetCookie(tabA, context, 'test@example.com')
      await tabB.goto('/camps')
      await expect(tabB).toHaveURL(/\/camps/)

      // Make the first camps request in tab A return 401, triggering a real token refresh
      let failed = false
      await context.route(/\/api\/camps/, async (route) => {
        if (!failed) {
          failed = true
          await route.fulfill({ status: 401, body: '' })
        } else {
          await route.continue()
        }
      })

      await tabA.goto('/camps')
      await expect(tabA).toHaveURL(/\/camps/, { timeout: 15000 })

      // After the token rotated in tab A, tab B must still work
      await tabB.reload()
      await expect(tabB).toHaveURL(/\/camps/, { timeout: 15000 })
    } finally {
      await context.close()
    }
  })
})
