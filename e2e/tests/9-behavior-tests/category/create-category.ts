import { expect } from '@playwright/test'
import { test } from '@/utils/etest'
import { Camp } from '@/utils/fixtures/domainObjects/camp'

test.describe('category on new camp', () => {
  let camp: Camp

  test.beforeEach(async ({ createCamp }) => {
    camp = await createCamp('Keine Vorlage')
  })

  test.afterEach(async () => {
    await camp?.delete()
  })

  test('creates a new category on the camp', async ({ runId, page }) => {
    const categoryName = `Test Category ${runId}`

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
