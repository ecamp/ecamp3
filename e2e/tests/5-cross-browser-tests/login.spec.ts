import { test, expect } from '@playwright/test'
import { mockDateNow } from '@/utils/helpers'

test.describe('Login test', () => {
  test.beforeEach(async ({ page }) => {
    await mockDateNow(page)
  })

  test('displays the login page', async ({ page }) => {
    await page.goto('/')
    await expect(page.locator('body')).toContainText('Login')
    await expect(page.locator('body')).toContainText(
      'This is the development version of eCamp v3.'
    )
    await expect(page.locator('body')).toContainText('Register now')
  })

  test('can login with default user', async ({ page }) => {
    await page.goto('/')

    await page.locator('[type="email"]').fill('test@example.com')
    await page.locator('[type="password"]').fill('test')
    await Promise.all([
      page.locator('[type="submit"]').click(),
      page.waitForURL('/camps', { timeout: 60000 }),
    ])

    await expect(page.locator('body')).toContainText('Meine Lager')
    await expect(page.locator('body')).toContainText('GRGR')
    await expect(page.locator('body')).toContainText('Harry Potter Lager')
  })
})
