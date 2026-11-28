import { expect } from '@playwright/test'
import { grgrCampId, jsKursPrototypeCampId } from '@/utils/constants'
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

  test('uses a typed manual URL when clipboard read fails', async ({
    clipboardStub,
    openCreateCampStep2,
  }) => {
    await clipboardStub.readFailure()
    const createCampDialogStep2 = await openCreateCampStep2()
    await createCampDialogStep2.selectOtherCampPrototype()
    await createCampDialogStep2.grantClipboardRead()
    await clipboardStub.expectReadSettled('rejected')
    await createCampDialogStep2.closeClipboardInfoDialog()
    await createCampDialogStep2.typeManualPrototypeUrl(copiedCampUrl)
    await createCampDialogStep2.expectCopiedPrototype()

    const { campPrototype } = await createCampDialogStep2.submit()
    expect(campPrototype).toBe(copiedCampUri)
  })

  test('blocks creating a camp while the manual URL is not a camp', async ({
    clipboardStub,
    openCreateCampStep2,
  }) => {
    await clipboardStub.readFailure()
    const createCampDialogStep2 = await openCreateCampStep2()
    await createCampDialogStep2.selectOtherCampPrototype()
    await createCampDialogStep2.grantClipboardRead()
    await clipboardStub.expectReadSettled('rejected')
    await createCampDialogStep2.closeClipboardInfoDialog()
    await createCampDialogStep2.fillUnknownManualPrototypeUrl('000000000000')
    await createCampDialogStep2.expectCreateCampBlocked()

    await createCampDialogStep2.fillManualPrototypeUrl(copiedCampUrl)
    await createCampDialogStep2.expectCopiedPrototype()
    const { campPrototype } = await createCampDialogStep2.submit()
    expect(campPrototype).toBe(copiedCampUri)
  })

  test('does not show clipboard text in the manual URL field', async ({
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

    await clipboardStub.readText('not a camp link', 'granted')
    const createCampDialogStep2 = await openCreateCampStep2()
    await clipboardStub.expectReadSettled('resolved')
    await createCampDialogStep2.selectOtherCampPrototype()
    await createCampDialogStep2.expectManualPrototypeUrl('')
  })

  test('submits a camp pasted with a pre-granted clipboard', async ({
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

  test('submits a camp URL auto-loaded from a pre-granted clipboard', async ({
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
    await clipboardStub.expectReadSettled('resolved')
    await createCampDialogStep2.expectCopiedPrototype()

    const { campPrototype } = await createCampDialogStep2.submit()
    expect(campPrototype).toBe(copiedCampUri)
  })

  test('lists a copied official camp template only once', async ({
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

    await clipboardStub.readText(
      `http://localhost:3000/camps/${jsKursPrototypeCampId}`,
      'granted'
    )
    const createCampDialogStep2 = await openCreateCampStep2()
    await clipboardStub.expectReadSettled('resolved')
    await createCampDialogStep2.expectSelectedPrototype('J+S Kurs')
    await createCampDialogStep2.expectPrototypeOptionCount('J+S Kurs', 1)
  })

  test('keeps a camp URL typed while the clipboard camp is still loading', async ({
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

    const clipboardCampPath = '/api/camps/000000000000'
    const isClipboardCampUrl = (url: URL) => url.pathname === clipboardCampPath
    let releaseClipboardCamp = () => {}
    const clipboardCampReleased = new Promise<void>((resolve) => {
      releaseClipboardCamp = resolve
    })
    await page.route(isClipboardCampUrl, async (route) => {
      await clipboardCampReleased
      await route.continue()
    })
    const waitForClipboardCampRequest = page.waitForRequest((request) =>
      isClipboardCampUrl(new URL(request.url()))
    )

    await clipboardStub.readText('http://localhost:3000/camps/000000000000', 'granted')
    const createCampDialogStep2 = await openCreateCampStep2()
    await waitForClipboardCampRequest
    await createCampDialogStep2.selectOtherCampPrototype()
    await createCampDialogStep2.fillManualPrototypeUrl(copiedCampUrl)
    await createCampDialogStep2.expectCopiedPrototype()

    const waitForClipboardCampResponse = page.waitForResponse((response) =>
      isClipboardCampUrl(new URL(response.url()))
    )
    releaseClipboardCamp()
    await waitForClipboardCampResponse
    await createCampDialogStep2.expectCopiedPrototype()

    const { campPrototype } = await createCampDialogStep2.submit()
    expect(campPrototype).toBe(copiedCampUri)
  })
})
