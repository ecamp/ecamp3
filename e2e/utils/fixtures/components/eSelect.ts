import { expect, Locator } from '@playwright/test'
import { boxedStep } from '@/utils/decorators/boxedStep'

export class ESelect {
  constructor(
    private readonly _locator: Locator,
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
    await this._page.getByRole('option', { name: value, exact: true }).click()
    await expect(this._activator).toHaveAttribute('aria-expanded', 'false')
    return this
  }

  get locator(): Locator {
    return this._locator
  }
}
