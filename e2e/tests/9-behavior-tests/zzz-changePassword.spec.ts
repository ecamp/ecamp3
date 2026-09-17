import { test, expect } from '@playwright/test'
import { loginAndSetCookie } from '@/utils/helpers'
import { castorUser } from '@/utils/constants'

const originalPassword = 'test'
const newPassword = 'new-password-test'
const restorePassword = 'testtest1234'

test('can change the password from the profile page', async ({ page, request }) => {
  await loginAndSetCookie(page, request, castorUser, originalPassword)

  await page.goto('/profile')
  await expect(page.locator('body')).toContainText('Profil:')

  await page
    .locator('.e-profile--password')
    .getByRole('button', { name: /Ändern/ })
    .click()
  const dialog = page.locator('.v-dialog.v-overlay--active')
  await expect(dialog).toContainText('Passwort ändern')

  // fill in the form: current password, new password, confirmation
  const passwordInputs = dialog.locator('input[type="password"]')
  await passwordInputs.nth(0).fill(originalPassword)
  await passwordInputs.nth(1).fill(newPassword)
  await passwordInputs.nth(2).fill(newPassword)

  await dialog.getByRole('button', { name: /Abschicken/ }).click()

  await expect(dialog).toContainText('Dein Passwort wurde erfolgreich geändert.')

  await dialog.getByRole('button', { name: /Schliessen/ }).click()

  await page.getByRole('button', { name: /Castor/ }).click()
  await page.getByRole('listitem').filter({ hasText: 'Ausloggen' }).click()
  await page.waitForURL('**/login', { timeout: 30000 })

  await page.locator('[type="email"]').fill(castorUser)
  await page.locator('[type="password"]').fill(newPassword)
  await Promise.all([
    page.locator('[type="submit"]').click(),
    page.waitForURL('/camps', { timeout: 60000 }),
  ])
  await expect(page.locator('body')).toContainText('Meine Lager')

  await page.goto('/profile')
  await page
    .locator('.e-profile--password')
    .getByRole('button', { name: /Ändern/ })
    .click()
  const restoreDialog = page.locator('.v-dialog.v-overlay--active')
  await expect(restoreDialog).toContainText('Passwort ändern')

  const restoreInputs = restoreDialog.locator('input[type="password"]')
  await restoreInputs.nth(0).fill(newPassword)
  await restoreInputs.nth(1).fill(restorePassword)
  await restoreInputs.nth(2).fill(restorePassword)

  await restoreDialog.getByRole('button', { name: /Abschicken/ }).click()
  await expect(restoreDialog).toContainText('Dein Passwort wurde erfolgreich geändert.')
})
