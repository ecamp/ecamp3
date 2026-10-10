import { expect, Page, Request } from '@playwright/test'
import { boxedStep } from '@/utils/decorators/boxedStep'
import { ESelect } from '@/utils/fixtures/components/eSelect'
import { CampInfo } from '@/utils/fixtures/pageObjects/camp/admin/campInfo'

export type CreatedCampCallback = (campInfo: CampInfo) => void

const ALLOW_BUTTON_LABEL = 'Jetzt erlauben'
const CLOSE_BUTTON_LABEL = 'Schliessen'
const PREVIEW_HEADING = 'Vorschau der Lagervorlage'
const OTHER_PROTOTYPE_OPTION = 'Einstellungen von einem anderen Lager kopieren…'
const PASTE_BUTTON_TITLE = 'Kopierte Lagereinstellungen einfügen'

export class CreateCampDialogStep2 {
  constructor(
    private readonly _page: Page,
    _form = _page.locator('form'),
    private readonly _prototypeSelect = new ESelect(
      _form.locator('div.v-input[data-testid="prototype-select"]')
    ),
    private readonly _createCampButton = _form.getByTestId('create-camp-button'),
    private readonly _allowButton = _page.getByRole('button', {
      name: ALLOW_BUTTON_LABEL,
    }),
    private readonly _pasteButton = _page.getByRole('button', {
      name: PASTE_BUTTON_TITLE,
    }),
    private readonly _closeButton = _page
      .locator('.v-overlay--active')
      .getByRole('button', { name: CLOSE_BUTTON_LABEL }),
    // frontend/src/locales/de.json
    private readonly _manualPrototypeUrlField = _page.getByLabel(
      'Link zum gewünschten Vorlage-Lager' // components.campCreate.campCreateStep2.prototypeCampUrl
    ),
    private readonly _previewBlock = _form.locator('.dashborder'),
    private readonly _previewContent = _previewBlock.locator('.v-list-item'),
    private readonly _previewHeading = _previewBlock.getByRole('heading', {
      name: PREVIEW_HEADING,
    }),
    private readonly _onCreatedCamp?: CreatedCampCallback
  ) {}

  @boxedStep
  async loaded() {
    await expect(this._prototypeSelect.locator).toBeVisible()
    return this
  }

  @boxedStep
  async selectPrototype(prototype: string) {
    await this._prototypeSelect.open()
    await this._prototypeSelect.select(prototype)
    return this
  }

  @boxedStep
  async selectOtherCampPrototype() {
    return this.selectPrototype(OTHER_PROTOTYPE_OPTION)
  }

  @boxedStep
  async grantClipboardRead() {
    await expect(this._allowButton).toBeVisible({ timeout: 30_000 })
    await this._allowButton.click({ timeout: 10_000 })
    return this
  }

  @boxedStep
  async pasteClipboardPrototype() {
    await this._pasteButton.click()
    return this
  }

  @boxedStep
  async closeClipboardInfoDialog() {
    await expect(this._closeButton).toBeVisible()
    await this._closeButton.click({ timeout: 10_000 })
    await expect(this._closeButton).toBeHidden({ timeout: 10_000 })
    return this
  }

  @boxedStep
  async fillManualPrototypeUrl(url: string) {
    await this._manualPrototypeUrlField.fill(url)
    return this
  }

  @boxedStep
  async expectSelectedPrototype(value: string) {
    await expect(this._prototypeSelect.locator).toContainText(value, { timeout: 30_000 })
    return this
  }

  @boxedStep
  async expectCopiedPrototype() {
    await this.expectSelectedPrototype('GRGR')
    await expect(this._previewHeading).toBeVisible({ timeout: 30_000 })
    await expect(this._previewContent.first()).toBeVisible({ timeout: 30_000 })
    return this
  }

  @boxedStep
  async submit(onCreatedCamp = this._onCreatedCamp) {
    const isCreateCamp = (request: Request) =>
      request.method() === 'POST' && new URL(request.url()).pathname === '/api/camps'
    const waitForCreateCampRequest = this._page.waitForRequest(isCreateCamp)
    const waitForCreateCampResponse = this._page.waitForResponse(
      (response) =>
        response.request().method() === 'POST' && isCreateCamp(response.request())
    )
    await this._createCampButton.click()
    const [createCampRequest, createCampResponse] = await Promise.all([
      waitForCreateCampRequest,
      waitForCreateCampResponse,
    ])
    expect(createCampResponse.ok()).toBe(true)
    const campPrototype = createCampRequest.postDataJSON().campPrototype

    const camp = await createCampResponse.json()
    const campId = camp.id
    if (!campId) {
      throw new Error('Could not extract camp id from create camp response')
    }

    const campInfo = new CampInfo(this._page, campId)
    onCreatedCamp?.(campInfo)
    await campInfo.loaded()
    return { campPrototype, campInfo }
  }
}
