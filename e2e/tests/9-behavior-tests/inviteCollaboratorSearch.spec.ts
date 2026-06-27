import { test, expect } from '@playwright/test'
import { bipiUser, grgrCampId } from '@/utils/constants'
import { loginAndSetCookie } from '@/utils/helpers'

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

    await expect(
      page.getByTestId('collaborator-invite-result').filter({ hasText: '@' })
    ).toHaveCount(0)
    await searchInput.blur()
    await expect(searchInput).toHaveValue('someone-new@example.com')
  })
})
