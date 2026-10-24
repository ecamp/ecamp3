import { expect, Locator, Page } from '@playwright/test'
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
    private readonly _heading = _page.getByRole('heading', { name: 'Meine Lager' }),
    private readonly _skeletonLoaders = _page.locator('.v-skeleton-loader')
  ) {}

  @boxedStep
  async loaded() {
    await this._page.waitForURL('/camps', { timeout: 15000 })
    await expect(this._createCampButton).toBeVisible()
    await expect(this._heading).toBeVisible()
    await expect(this._skeletonLoaders).toHaveCount(0)
    return this
  }

  @boxedStep
  async openCreateCampDialog() {
    await this._createCampButton.click()
    const createCampDialogStep1 = new CreateCampDialogStep1(this._page)
    await createCampDialogStep1.loaded()
    return createCampDialogStep1
  }

  get heading(): Locator {
    return this._heading
  }

  loggedInUserButton(displayName: string): Locator {
    return this._page.getByRole('button').filter({ hasText: displayName })
  }

  campTitle(campTitle: string): Locator {
    return this._page.getByText(campTitle, { exact: true })
  }
}
