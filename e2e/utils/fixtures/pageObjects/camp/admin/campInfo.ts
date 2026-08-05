import { expect, Locator, Page } from '@playwright/test'

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

  get campId(): string {
    return this._campId
  }

  get titleField(): Locator {
    return this._titleField
  }
}
