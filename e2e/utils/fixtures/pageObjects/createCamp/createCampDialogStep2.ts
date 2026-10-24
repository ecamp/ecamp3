import { expect, Page } from '@playwright/test'
import { boxedStep } from '@/utils/decorators/boxedStep'
import { ESelect } from '@/utils/fixtures/components/eSelect'
import { CampInfo } from '@/utils/fixtures/pageObjects/camp/admin/campInfo'

export class CreateCampDialogStep2 {
  constructor(
    private readonly _page: Page,
    _form = _page.locator('form'),
    private readonly _prototypeSelect = new ESelect(
      _form.locator('div.v-input[data-testid="prototype-select"]')
    ),
    private readonly _createCampButton = _form.getByTestId('create-camp-button')
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
  async submit() {
    const waitForCampInfoRoute = this._page.waitForURL(`**${CampInfo.ROUTE}`, {
      timeout: 60000,
    })
    await this._createCampButton.click()
    await waitForCampInfoRoute

    const url = this._page.url()
    const match = url.match(new RegExp(`/camps/([^/]+)/.*${CampInfo.ROUTE}`))
    const campId = match?.[1]
    if (!campId) {
      throw new Error(`Could not extract camp id from URL: ${url}`)
    }

    const campInfo = new CampInfo(this._page, campId)
    await campInfo.loaded()
    return campInfo
  }
}
