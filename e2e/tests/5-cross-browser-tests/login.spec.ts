import { expect } from '@playwright/test'
import { bipiUser } from '@/utils/constants'
import { test } from '@/utils/etest'

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

test.describe('Login test through page objects', () => {
  test('displays the login page via page objects', async ({ loginPage }) => {
    await loginPage.open()

    await expect(loginPage.locator).toContainText('Login')
    await expect(loginPage.locator).toContainText(
      'This is the development version of eCamp v3.'
    )
    await expect(loginPage.locator).toContainText('Register now')
  })

  test('can login with default user via page objects', async ({ loginPage }) => {
    await loginPage.open()
    const campListPage = await loginPage.loginToCampList(bipiUser)

    await expect(campListPage.loggedInUserButton('Bi-Pi')).toBeVisible()
    await expect(campListPage.heading).toBeVisible()
    await expect(campListPage.campTitle('GRGR')).toBeVisible()
    await expect(campListPage.campTitle('Harry Potter Lager')).toBeVisible()
  })
})
