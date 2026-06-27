import { test, expect } from '@playwright/test'
import { bipiUser, grgrCampId } from '@/utils/constants'
import { loginAndSetCookie } from '@/utils/helpers'

/**
 * Covers ecamp/ecamp3#340: the collaborator invite dialog lets a manager search the
 * profiles they share a camp with (by firstname, surname, nickname or email) and pick one,
 * which fills in the invite email address. Free email addresses can still be typed.
 *
 * In the GRGR camp, Bi-Pi (test@example.com) shares the camp with Fritz Müller (nickname
 * "Salamander", salamander@example.com), so searching for that person must find them.
 */
test.describe('invite collaborator by searching profiles', { tag: '@mature' }, () => {
  test.beforeEach(async ({ page, request }) => {
    await loginAndSetCookie(page, request, bipiUser)
    await page.goto(`/camps/${grgrCampId}/admin/collaborators`)
    await page.getByTestId('collaborator-invite-cta').click()
  })

  test('finds a related profile by nickname and uses its email', async ({ page }) => {
    const searchInput = page.locator('[data-testid="collaborator-invite-search"] input')
    await searchInput.fill('Salamander')

    const result = page
      .getByTestId('collaborator-invite-result')
      .filter({ hasText: 'salamander@example.com' })
    await expect(result).toBeVisible({ timeout: 10000 })
    await expect(result).toContainText('Fritz Müller')

    await result.click()

    // After selection, the email address is the value used for the invitation.
    await expect(searchInput).toHaveValue('salamander@example.com')
  })

  test('finds a related profile by part of the surname', async ({ page }) => {
    const searchInput = page.locator('[data-testid="collaborator-invite-search"] input')
    await searchInput.fill('müll')

    const result = page
      .getByTestId('collaborator-invite-result')
      .filter({ hasText: 'salamander@example.com' })
    await expect(result).toBeVisible({ timeout: 10000 })
  })

  test('allows typing a brand-new email address that is not a known profile', async ({
    page,
  }) => {
    const searchInput = page.locator('[data-testid="collaborator-invite-search"] input')
    await searchInput.fill('someone-new@example.com')

    // No known profile matches, so the typed email can simply be kept as the value.
    await expect(
      page.getByTestId('collaborator-invite-result').filter({ hasText: '@' })
    ).toHaveCount(0)
    await searchInput.blur()
    await expect(searchInput).toHaveValue('someone-new@example.com')
  })
})
