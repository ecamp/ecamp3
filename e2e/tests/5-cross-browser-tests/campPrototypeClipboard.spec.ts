import { expect } from '@playwright/test'
import { grgrCampId } from '@/utils/constants'
import { test } from '@/utils/etest'

const copiedCampUrl = `http://localhost:3000/camps/${grgrCampId}/GRGR/dashboard`
const copiedCampUri = `/camps/${grgrCampId}`

test.describe('camp prototype clipboard', { tag: '@mature' }, () => {
  test.afterEach(async ({ cleanupCamps }) => {
    await cleanupCamps()
  })

  test('loads a copied camp URL from the clipboard', async ({
    browserName,
    clipboardStub,
    openCreateCampStep2,
  }) => {
    //eslint-disable-next-line playwright/no-conditional-in-test
    if (browserName !== 'chromium') {
      // Only chromium supports clipboard read.
      //eslint-disable-next-line
      test.skip()
    }

    await clipboardStub.readText(copiedCampUrl)
    const createCampDialogStep2 = await openCreateCampStep2()
    await createCampDialogStep2.selectOtherCampPrototype()
    await createCampDialogStep2.grantClipboardRead()
    await clipboardStub.expectReadSettled('resolved')
    await createCampDialogStep2.closeClipboardInfoDialog()

    await createCampDialogStep2.expectCopiedPrototype()

    const { campPrototype } = await createCampDialogStep2.submit()
    expect(campPrototype).toBe(copiedCampUri)
  })

  test('uses the manual URL when clipboard read fails', async ({
    clipboardStub,
    openCreateCampStep2,
  }) => {
    await clipboardStub.readFailure()
    const createCampDialogStep2 = await openCreateCampStep2()
    await createCampDialogStep2.selectOtherCampPrototype()
    await createCampDialogStep2.grantClipboardRead()
    await clipboardStub.expectReadSettled('rejected')
    await createCampDialogStep2.closeClipboardInfoDialog()
    await createCampDialogStep2.fillManualPrototypeUrl(copiedCampUrl)
    await createCampDialogStep2.expectCopiedPrototype()

    const { campPrototype } = await createCampDialogStep2.submit()
    expect(campPrototype).toBe(copiedCampUri)
  })

  test('does not submit a pre-granted auto-loaded clipboard prototype', async ({
    page,
    browserName,
    clipboardStub,
    openCreateCampStep2,
  }) => {
    //eslint-disable-next-line playwright/no-conditional-in-test
    if (browserName !== 'chromium') {
      // Only chromium supports clipboard read.
      //eslint-disable-next-line
      test.skip()
    }

    await clipboardStub.readText(copiedCampUrl, 'granted')
    const createCampDialogStep2 = await openCreateCampStep2()
    await createCampDialogStep2.selectOtherCampPrototype()
    await createCampDialogStep2.pasteClipboardPrototype()
    await clipboardStub.expectReadSettled('resolved')
    await createCampDialogStep2.expectCopiedPrototype()

    await expect(page).toHaveURL(/\/camps\/create$/)

    const { campPrototype } = await createCampDialogStep2.submit()
    expect(campPrototype).toBe(copiedCampUri)
  })
})
