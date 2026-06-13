import { expect } from '@playwright/test'
import { bipiUser } from '@/utils/constants'
import {
  loginAndSetCookie,
  mockDateNow,
  createCampViaUI,
  deleteCampViaUI,
} from '@/utils/helpers'
import { test } from '@/utils/etest'

const campTitle = 'CatTestCamp'

test.describe('category on new camp', () => {
  test.describe.configure({ mode: 'serial' })

  let campAdminBaseUrl: string

  test.beforeAll(async ({ browser }) => {
    const context = await browser.newContext()
    const page = await context.newPage()
    await mockDateNow(page)
    await loginAndSetCookie(page, null, bipiUser)
    campAdminBaseUrl = await createCampViaUI(page, campTitle)
    await context.close()
  })

  test.afterAll(async ({ browser }) => {
    if (!campAdminBaseUrl) return
    const context = await browser.newContext()
    const page = await context.newPage()
    await loginAndSetCookie(page, null, bipiUser)
    await deleteCampViaUI(page, campAdminBaseUrl, campTitle)
    await context.close()
  })

  test('creates a new category on the camp', async ({ page, request, runId }) => {
    const categoryName = `Test Category ${runId}`
    await mockDateNow(page)
    await loginAndSetCookie(page, request, bipiUser)

    await page.goto(`${campAdminBaseUrl}/activity`)

    const createButton = page.getByRole('button', { name: /Block-Kategorie erstellen/i })
    await expect(createButton).toBeVisible({ timeout: 15000 })
    await createButton.click()

    const dialog = page.locator('.v-overlay--active')
    await expect(dialog).toBeVisible({ timeout: 10000 })

    await dialog.locator('[name="short"] input').fill('TC')
    await dialog.locator('[name="name"] input').fill(categoryName)

    await dialog.getByRole('button', { name: /Erstellen/i }).click()

    await expect(dialog).toBeHidden({ timeout: 10000 })
    await expect(page.getByText(categoryName, { exact: true }).first()).toBeVisible({
      timeout: 10000,
    })

    await page.goto(`${campAdminBaseUrl}/activity`)
    await expect(page.getByText(categoryName)).toBeVisible()
  })
})
