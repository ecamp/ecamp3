import { expect, Locator } from '@playwright/test'
import { boxedStep } from '@/utils/decorators/boxedStep'

export class ESelect {
  constructor(
    private readonly _locator: Locator,
    private readonly _selectOpenLocator = _locator.page().locator('.v-overlay--active'),
    private readonly _selectClosedLocator = _locator.page().locator('.v-overlay')
  ) {}

  @boxedStep
  async open() {
    await this._locator.click()
    await expect(this._selectOpenLocator).toBeVisible({
      timeout: 10000,
    })
    return this
  }

  @boxedStep
  async select(value: string) {
    await this._selectOpenLocator.getByText(value, { exact: true }).click()

    await expect(this._selectClosedLocator).toBeHidden({
      timeout: 10000,
    })

    return this
  }

  get locator(): Locator {
    return this._locator
  }
}
