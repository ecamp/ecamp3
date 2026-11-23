import { expect, Locator } from '@playwright/test'
import { boxedStep } from '@/utils/decorators/boxedStep'

export class ESelect {
  constructor(
    private readonly _locator: Locator,
    private readonly _selectOpenLocator = _locator.page().locator('.v-overlay--active'),
    private readonly _selectClosedLocator = _locator
      .page()
      .locator('.v-overlay--active:not(.v-dialog)'),
    private readonly _activator = _locator.locator('input[aria-expanded]'),
    private readonly _page = _locator.page()
  ) {}

  @boxedStep
  async open() {
    await this._locator.click()
    await expect(this._activator).toHaveAttribute('aria-expanded', 'true')
    return this
  }

  @boxedStep
  async select(value: string) {
    const option = this.option(value)
    await expect(option).toBeVisible()
    await option.click()
    await expect(this._activator).toHaveAttribute('aria-expanded', 'false')
    return this
  }

  option(value: string): Locator {
    return this._page.getByRole('option', { name: value, exact: true })
  }

  get locator(): Locator {
    return this._locator
  }
}
