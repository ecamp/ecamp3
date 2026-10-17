import { expect, Locator, Page } from '@playwright/test'
import { boxedStep } from '@/utils/decorators/boxedStep'
import { DialogDeleteCamp } from '@/utils/fixtures/pageObjects/camp/admin/dialogDeleteCamp'

export class CampInfo {
  static readonly ROUTE = '/admin/info'

  constructor(
    private readonly _page: Page,
    private readonly _campId: string,
    private readonly _titleField = _page.locator('[data-testid="title"] input'),
    private readonly _dangerZoneTitle = _page
      .locator('.v-expansion-panel')
      .filter({ hasText: 'Gefahrenzone' })
      .locator('.v-expansion-panel-title'),
    private readonly _deleteCampButton = _page
      .locator('.v-expansion-panel-text')
      .getByRole('button', { name: /Löschen/i })
  ) {}

  async goto() {
    await this._page.goto(`/camps/${this._campId}${CampInfo.ROUTE}`)
    await this.loaded()
    return this
  }

  async loaded() {
    await expect(this._titleField).toBeVisible({ timeout: 45_000 })
  }
  @boxedStep
  async openDeleteDialog() {
    const expanded = await this._dangerZoneTitle.getAttribute('aria-expanded', {
      timeout: 5_000,
    })
    if (expanded !== 'true') {
      await this._dangerZoneTitle.click({ timeout: 10_000 })
    }
    await expect(this._deleteCampButton).toBeVisible({ timeout: 10_000 })
    await this._deleteCampButton.click({ timeout: 10_000 })

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
