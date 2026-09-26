import { test, expect } from '@playwright/test'

test.describe('Login test', { tag: '@mature' }, () => {
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
    await page.locator('[type="submit"]').click()

    await expect(page).toHaveURL((url) => url.pathname === '/camps')
    await expect(page.getByRole('heading', { name: 'Meine Lager' })).toBeVisible()
    await expect(page.getByText('GRGR', { exact: true })).toBeVisible()
    await expect(page.getByText('Harry Potter Lager', { exact: true })).toBeVisible()
  })
})
