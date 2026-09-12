import { test } from '@/utils/etest'
import { expect } from '@playwright/test'

test('renders login page', async ({ loginPage }) => {
  await loginPage.open()

  await loginPage.emailField.fill('averylongemail@long-domain.com')
  await loginPage.passwordField.fill('mysupersecretpassword')

  await expect(loginPage.locator).toHaveScreenshot()
})
