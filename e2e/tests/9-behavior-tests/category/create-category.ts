import { expect } from '@playwright/test'
import { test } from '@/utils/etest'

test.describe('category on new camp', () => {
  test('creates a new category on the camp', async ({ createCamp, runId, page }) => {
    const categoryName = `Test Category ${runId}`
    const camp = await createCamp('Keine Vorlage')

    const campActivitySettings = camp.campActivitySettings
    const campCategories = await campActivitySettings.goTo()
    const dialog = await campCategories.openCreateCategoryDialog()
    await dialog.fillForm('TC', categoryName)
    await dialog.submit()

    await expect(page.getByText(categoryName).first()).toBeVisible({
      timeout: 15000,
    })

    await campActivitySettings.goTo()
    await campActivitySettings.expectCategoryVisible(categoryName)

    await page.reload()
    await campActivitySettings.loaded()
    await campActivitySettings.expectCategoryVisible(categoryName)
  })
})
