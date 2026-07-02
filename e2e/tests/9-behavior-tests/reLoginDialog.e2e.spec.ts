import { test, expect } from '@playwright/test'
import { loginAndSetCookie, makeExpiredJwt } from '@/utils/helpers'

const COOKIE_PREFIX = 'localhost_'
const S_COOKIE = `${COOKIE_PREFIX}jwt_s`
const HP_COOKIE = `${COOKIE_PREFIX}jwt_hp`

test.describe('re-login dialog', () => {
  test.beforeEach(async ({ page, context }) => {
    await loginAndSetCookie(page, context, 'test@example.com')

    let apiCampIntercepted = false
    await page.route(/\/api\/camps/, (route) => {
      if (!apiCampIntercepted) {
        apiCampIntercepted = true
        return route.fulfill({ status: 401, body: '' })
      }
      route.continue()
    })

    let refreshIntercepted = false
    await page.route(/\/token\/refresh$/, (route) => {
      if (!refreshIntercepted) {
        refreshIntercepted = true
        return route.fulfill({ status: 401, body: '' })
      }
      route.continue()
    })

    await context.clearCookies({ name: S_COOKIE })
    await page.reload()
    await expect(page.getByText('Sitzung abgelaufen')).toBeVisible({ timeout: 15000 })

    // clear the HP cookie at the end, else we get thrown to the login page.
    await context.clearCookies({ name: HP_COOKIE })
    await context.addCookies([
      { name: HP_COOKIE, value: makeExpiredJwt(), url: 'http://localhost:3000' },
    ])
  })

  test('dialog appears when session expires mid-session', async ({ page }) => {
    await expect(page.getByText('Sitzung abgelaufen')).toBeVisible()
    await expect(page).toHaveURL(/\/camps/)
  })

  test('re-login in new tab closes dialog and stays on page', async ({
    page,
    context,
  }) => {
    const newTabPromise = context.waitForEvent('page')
    await page.getByRole('button', { name: /Anmeld/i }).click()
    const loginTab = await newTabPromise

    const loginButton = loginTab.getByRole('button', { name: 'Login' })
    await expect(loginButton).toBeVisible({ timeout: 10_000 })

    await loginButton.click()
    await expect(loginTab).toHaveURL((url) => url.pathname !== '/login')
    const campRows = loginTab.locator('.v-list-item[role="link"]')
    await expect.poll(async () => campRows.count()).toBeGreaterThan(3)

    await expect(page.getByText('Sitzung abgelaufen')).toBeHidden({ timeout: 5000 })
    await expect(page).toHaveURL(/\/camps/)
  })

  test('logout button in dialog navigates to login page', async ({ page }) => {
    await page.getByRole('button', { name: 'Ausloggen' }).click()

    await expect(page).toHaveURL(/\/login/)
  })
})

test('dialog is not shown when the refresh token silently renews the session', async ({
  page,
  context,
}) => {
  await loginAndSetCookie(page, context, 'test@example.com')
  await context.clearCookies({ name: HP_COOKIE })
  await context.addCookies([
    { name: HP_COOKIE, value: makeExpiredJwt(), url: 'http://localhost:3000' },
  ])

  await page.goto('/camps')

  await expect(page.getByText('Sitzung abgelaufen')).toBeHidden()
  await expect(page).toHaveURL(/\/camps/, { timeout: 15000 })
})
