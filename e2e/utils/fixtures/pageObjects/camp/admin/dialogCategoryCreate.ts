import { expect, Page } from '@playwright/test'
import { boxedStep } from '@/utils/decorators/boxedStep'

export class DialogCategoryCreate {
  constructor(
    private readonly _page: Page,
    _dialog = _page.locator('.v-overlay--active'),
    private readonly _shortInput = _dialog.locator('[name="short"] input'),
    private readonly _nameInput = _dialog.locator('[name="name"] input'),
    private readonly _createButton = _dialog.getByRole('button', {
      name: /Erstellen/i,
    })
  ) {}

  @boxedStep
  async loaded() {
    await expect(this._shortInput).toBeVisible({ timeout: 10000 })
    return this
  }

  @boxedStep
  async fillForm(short: string, name: string) {
    await this._shortInput.fill(short)
    await this._nameInput.fill(name)
    return this
  }

  @boxedStep
  async submit() {
    await this._createButton.click()
    // App navigates to category detail page after creation
    await this._page.waitForURL('**/camps/**/category/**', {
      waitUntil: 'domcontentloaded',
      timeout: 60000,
    })
  }
}
