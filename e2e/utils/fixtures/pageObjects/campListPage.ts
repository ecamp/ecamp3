import { expect, Page } from '@playwright/test'
import { boxedStep } from '@/utils/decorators/boxedStep'
import { CreateCampDialogStep1 } from '@/utils/fixtures/pageObjects/createCamp/createCampDialogStep1'

export type CampListPageFixtureType = {
  camplistPage: CampListPage
}

// noinspection JSUnusedGlobalSymbols
export const camplistPageFixture = {
  camplistPage: async (
    { page }: { page: Page },
    use: (a: CampListPageFixtureType['camplistPage']) => Promise<void>
  ) => {
    await use(new CampListPage(page))
  },
}

export class CampListPage {
  constructor(
    private readonly _page: Page,
    private readonly _createCampButton = _page.getByTestId('create-camp-button'),
    private readonly _campListSkeletons = _page.locator('.v-skeleton-loader')
  ) {}

  @boxedStep
  async goto() {
    await this._page.goto('/camps')
    return this.loaded()
  }

  @boxedStep
  async loaded() {
    await expect(this._createCampButton).toBeVisible()
    await expect(this._campListSkeletons).toHaveCount(0, { timeout: 10_000 })
    return this
  }

  @boxedStep
  async openCreateCampDialog() {
    await this._createCampButton.click()
    const createCampDialogStep1 = new CreateCampDialogStep1(this._page)
    await createCampDialogStep1.loaded()
    return createCampDialogStep1
  }

  @boxedStep
  async expectCampNotListed(campTitle: string) {
    await expect(this._page.getByText(campTitle)).toBeHidden()
    return this
  }
}
