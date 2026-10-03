import { expect, type APIRequestContext, type Page } from '@playwright/test'
import { bipiUser, grgrCampId } from '@/utils/constants'
import { loginAndSetCookie } from '@/utils/helpers'
import { test } from '@/utils/etest'

const copiedCampUrl = `http://localhost:3000/camps/${grgrCampId}/GRGR/dashboard`
const copiedCampUri = `/camps/${grgrCampId}`
const otherCampPrototype = 'Einstellungen von einem anderen Lager kopieren…'
const pasteCamp = 'Kopierte Lagereinstellungen einfügen'
const manualPrototypeUrl = 'Link zum gewünschten Vorlage-Lager'

test.describe('camp prototype clipboard', () => {
  test('loads a copied camp URL from the clipboard', async ({ page, request, runId }) => {
    await stubClipboardReadText(page, copiedCampUrl)
    await openCampCreatePrototypeStep(page, request, `clipboard prototype ${runId}`)

    await selectOtherCampPrototype(page)
    await requestClipboardPrototypePaste(page)
    await expectCopiedCampPrototype(page)

    const body = await waitForCreateCampRequest(page)
    expect(body.campPrototype).toBe(copiedCampUri)
  })

  test('uses the manual URL when clipboard read fails', async ({
    page,
    request,
    runId,
  }) => {
    await stubClipboardReadFailure(page)
    await openCampCreatePrototypeStep(page, request, `manual prototype ${runId}`)

    await selectOtherCampPrototype(page)
    await requestClipboardPrototypePaste(page)
    await fillManualPrototypeUrl(page)
    await expectCopiedCampPrototype(page)

    const body = await waitForCreateCampRequest(page)
    expect(body.campPrototype).toBe(copiedCampUri)
  })

  test('does not submit a pre-granted auto-loaded clipboard prototype', async ({
    page,
    request,
    runId,
  }) => {
    await stubClipboardReadText(page, copiedCampUrl, 'granted')
    await openCampCreatePrototypeStep(page, request, `auto-loaded prototype ${runId}`)

    await expect(
      page.locator('div.v-input[data-testid="prototype-select"]')
    ).toContainText('GRGR')

    const body = await waitForCreateCampRequest(page)
    expect(body.campPrototype).toBeNull()
  })
})

async function stubClipboardReadText(
  page: Page,
  text: string,
  permissionState?: PermissionState
) {
  await page.addInitScript(
    ({ clipboardText, clipboardPermissionState }) => {
      Object.defineProperty(navigator, 'clipboard', {
        configurable: true,
        value: {
          ...navigator.clipboard,
          readText: async () => clipboardText,
          writeText: async () => {},
        },
      })

      if (clipboardPermissionState == null) return

      const permissions = navigator.permissions
      Object.defineProperty(navigator, 'permissions', {
        configurable: true,
        value: {
          ...permissions,
          query: async (descriptor: { name: string }) => {
            if (descriptor.name === 'clipboard-read') {
              return { state: clipboardPermissionState }
            }
            return permissions.query(descriptor as PermissionDescriptor)
          },
        },
      })
    },
    { clipboardText: text, clipboardPermissionState: permissionState }
  )
}

async function stubClipboardReadFailure(page: Page) {
  await page.addInitScript(() => {
    Object.defineProperty(navigator, 'clipboard', {
      configurable: true,
      value: {
        ...navigator.clipboard,
        readText: async () => {
          throw new DOMException('denied', 'NotAllowedError')
        },
        writeText: async () => {},
      },
    })
  })
}

async function openCampCreatePrototypeStep(
  page: Page,
  request: APIRequestContext,
  campTitle: string
) {
  await loginAndSetCookie(page, request, bipiUser)
  await page.getByTestId('create-camp-button').click()

  await page.locator('[data-testid="create-camp-title-input"] input').fill(campTitle)
  await page.locator('[data-testid="create-camp-organizer"] input').fill('org')
  await page.locator('[data-testid="create-camp-motto"] input').fill('motto')

  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)
  const in2Days = new Date()
  in2Days.setDate(in2Days.getDate() + 2)

  await page
    .locator('[data-testid="start-date-picker"] input')
    .fill(tomorrow.toLocaleDateString('de-CH'))
  await page
    .locator('[data-testid="end-date-picker"] input')
    .fill(in2Days.toLocaleDateString('de-CH'))
  await page.locator('[data-testid="create-camp-next-step"]').click()
}

async function selectOtherCampPrototype(page: Page) {
  await page.locator('div.v-input[data-testid="prototype-select"]').click()
  await page.getByText(otherCampPrototype).click()
}

async function requestClipboardPrototypePaste(page: Page) {
  const allowButton = page.getByRole('button', { name: 'Jetzt erlauben' })

  if (await allowButton.isVisible({ timeout: 1000 }).catch(() => false)) {
    await allowButton.click()
    const closeButton = page.getByRole('button', { name: 'Schliessen' })
    if (await closeButton.isVisible({ timeout: 1000 }).catch(() => false)) {
      await closeButton.click()
    }
    return
  }

  await page.locator(`button[title="${pasteCamp}"]`).click()
}

async function fillManualPrototypeUrl(page: Page) {
  await page.getByLabel(manualPrototypeUrl).fill(copiedCampUrl)
}

async function waitForCreateCampRequest(page: Page) {
  const createCampRequest = page.waitForRequest((request) => {
    return request.method() === 'POST' && new URL(request.url()).pathname === '/api/camps'
  })

  await page.getByTestId('create-camp-button').click()

  return (await createCampRequest).postDataJSON() as { campPrototype?: string | null }
}

async function expectCopiedCampPrototype(page: Page) {
  await expect(page.getByText('Vorschau der Lagervorlage')).toBeVisible()
  await expect(page.locator('div.v-input[data-testid="prototype-select"]')).toContainText(
    'GRGR'
  )
}
