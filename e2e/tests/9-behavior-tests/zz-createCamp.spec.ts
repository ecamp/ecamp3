import { expect } from '@playwright/test'
import { bipiUser } from '@/utils/constants'
import { test } from '@/utils/etest'

const tomorrow = new Date()
tomorrow.setDate(tomorrow.getDate() + 1)

const in2Days = new Date()
in2Days.setDate(in2Days.getDate() + 2)

test('without prototype', { tag: '@mature' }, async ({ createCamp }) => {
  const camp = await createCamp('Keine Vorlage')
  const campInfo = camp.campInfo

  await campInfo.goto()

  await expect(camp.campInfo.titleField).toHaveValue(camp.campTitle)
})

test(
  'without prototype asserting the noPrototypeAlert',
  { tag: '@mature' },
  async ({ loginPage, runId }) => {
    const title = `camp ${runId}`

    await loginPage.open()
    const campListPage = await loginPage.loginToCampList(bipiUser)

    await expect(campListPage.campTitle('GRGR')).toBeVisible()

    const createCampDialogStep1 = await campListPage.openCreateCampDialog()
    await createCampDialogStep1.fillForm(tomorrow, in2Days, title)

    const createCampDialogStep2 = await createCampDialogStep1.next()
    await createCampDialogStep2.selectPrototype('Keine Vorlage')

    await expect(createCampDialogStep2.noPrototypeAlert).toBeVisible()

    const { campInfo } = await createCampDialogStep2.submit()

    await expect(campInfo.heading).toBeVisible()
    await expect(campInfo.titleField).toHaveValue(title)
  }
)
