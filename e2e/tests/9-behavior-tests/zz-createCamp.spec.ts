import { expect } from '@playwright/test'
import { bipiUser } from '@/utils/constants'
import { loginAndSetCookie } from '@/utils/helpers'
import { test } from '@/utils/etest'

const tomorrow = new Date()
tomorrow.setDate(tomorrow.getDate() + 1)

const in2Days = new Date()
in2Days.setDate(in2Days.getDate() + 2)

const campTitle = 'title'

test.describe('create new camp', { tag: '@mature' }, () => {
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

    await page.waitForURL('**')
    await expect(page).toHaveURL((url) => url.pathname.endsWith('/info'))

    await expect(page.locator('main >> text=Lagerinfos')).toBeVisible()
    await expect(page.locator('[data-testid="title"] input')).toHaveValue(campTitle)
  })
})

test('without prototype via page objects', async ({ createCamp }) => {
  const camp = await createCamp('Keine Vorlage')
  const campInfo = camp.campInfo

  await campInfo.goto()

  await expect(camp.campInfo.titleField).toHaveValue(camp.campTitle)
})

test('without prototype step by step via page objects', async ({ loginPage, runId }) => {
  const title = `camp ${runId}`

  await loginPage.open()
  const campListPage = await loginPage.loginToCampList(bipiUser)

  await expect(campListPage.campTitle('GRGR')).toBeVisible()

  const createCampDialogStep1 = await campListPage.openCreateCampDialog()
  await createCampDialogStep1.fillForm(tomorrow, in2Days, title)

  const createCampDialogStep2 = await createCampDialogStep1.next()
  await createCampDialogStep2.selectPrototype('Keine Vorlage')

  await expect(createCampDialogStep2.noPrototypeAlert).toBeVisible()

  const campInfo = await createCampDialogStep2.submit()

  await expect(campInfo.heading).toBeVisible()
  await expect(campInfo.titleField).toHaveValue(title)
})
