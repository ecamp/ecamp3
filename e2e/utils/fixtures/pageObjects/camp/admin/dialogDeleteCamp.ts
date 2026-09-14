import { expect, Page } from '@playwright/test'
import { boxedStep } from '@/utils/decorators/boxedStep'

export class DialogDeleteCamp {
  constructor(
    private readonly _page: Page,
    _dialog = _page.locator('.v-overlay--active'),
    private readonly _promptInput = _dialog.locator('[name="promptText"] input'),
    private readonly _deleteButton = _dialog.getByRole('button', {
      name: /Löschen/i,
    })
  ) {}

  @boxedStep
  async loaded() {
    await expect(this._promptInput).toBeVisible({ timeout: 10000 })
    return this
  }

  @boxedStep
  async fillPrompt(campTitle: string) {
    await this._promptInput.fill(campTitle)
    return this
  }

  @boxedStep
  async submit() {
    await this._deleteButton.click()
    await this._page.waitForURL(/\/camps$/, { timeout: 15000 })
  }
}
