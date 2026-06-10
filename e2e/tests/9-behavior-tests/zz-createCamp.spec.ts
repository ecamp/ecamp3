import { test, expect } from '@playwright/test'
import { bipiUser } from '@/utils/constants'
import { loginAndSetCookie, mockDateNow } from '@/utils/helpers'

const tomorrow = new Date()
tomorrow.setDate(tomorrow.getDate() + 1)

const in2Days = new Date()
in2Days.setDate(in2Days.getDate() + 2)

const campTitle = 'title'

test.describe('create new camp', () => {
  test.beforeEach(async ({ page }) => {
    await mockDateNow(page)
  })

  test('without prototype', async ({ page, request }) => {
    await loginAndSetCookie(page, request, bipiUser)
    await expect(page.locator('body')).toContainText('GRGR')

    await page.getByTestId('create-camp-button').click()

    await page.locator('[data-testid="create-camp-title-input"] input').fill(campTitle)
    await page.locator('[data-testid="create-camp-organizer"] input').fill('org')
    await page.locator('[data-testid="create-camp-motto"] input').fill('motto')
    await page
      .locator('[data-testid="start-date-picker"] input')
      .fill(tomorrow.toLocaleDateString('de-CH'))
    await page
      .locator('[data-testid="end-date-picker"] input')
      .fill(in2Days.toLocaleDateString('de-CH'))

    await page.locator('[data-testid="create-camp-next-step"]').click()
    await page.locator('div.v-input[data-testid="prototype-select"]').click()
    await expect(page.locator('.v-overlay--active')).toBeVisible({ timeout: 10000 })
    await page.locator('text=Keine Vorlage').click()
    await expect(
      page.locator('text=Achtung: Du hast "Keine Vorlage" ausgewählt.')
    ).toBeVisible()
    await expect(page.locator('.v-overlay')).not.toBeVisible({ timeout: 10000 })
    await page.getByTestId('create-camp-button').click()

    await page.waitForURL('**/info')

    await expect(page.locator('main >> text=Lagerinfos')).toBeVisible()
    await expect(page.locator('[data-testid="title"] input')).toHaveValue(campTitle)
  })
})
