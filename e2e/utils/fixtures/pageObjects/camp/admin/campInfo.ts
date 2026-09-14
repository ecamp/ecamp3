import { expect, Locator, Page } from '@playwright/test'
import { boxedStep } from '@/utils/decorators/boxedStep'
import { DialogDeleteCamp } from '@/utils/fixtures/pageObjects/camp/admin/dialogDeleteCamp'

export class CampInfo {
  static readonly ROUTE = '/admin/info'

  constructor(
    private readonly _page: Page,
    private readonly _campId: string,
    private readonly _titleField = _page.locator('[data-testid="title"] input')
  ) {}

  async goto() {
    await this._page.goto(`/camps/${this._campId}${CampInfo.ROUTE}`)
    await this.loaded()
    return this
  }

  async loaded() {
    await expect(this._titleField).toBeVisible()
  }

  @boxedStep
  async openDeleteDialog() {
    await this._page
      .locator('.v-expansion-panel')
      .filter({ hasText: 'Gefahrenzone' })
      .locator('.v-expansion-panel-title')
      .click()

    await this._page
      .locator('.v-expansion-panel-text')
      .getByRole('button', { name: /Löschen/i })
      .click()

    const dialog = new DialogDeleteCamp(this._page)
    await dialog.loaded()
    return dialog
  }

  get campId(): string {
    return this._campId
  }

  get titleField(): Locator {
    return this._titleField
  }
}
