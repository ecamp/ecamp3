import { expect, Locator, Page } from '@playwright/test'
import { boxedStep } from '@/utils/decorators/boxedStep'

export const loginPageFixture = {
  loginPage: async (
    { page }: { page: Page },
    use: (loginPage: LoginPage) => Promise<void>
  ) => {
    await use(new LoginPage(page))
  },
}

export type LoginPageFixtureType = {
  loginPage: LoginPage
}

export class LoginPage {
  private readonly _page: Page
  private readonly _quickLoginButton: Locator
  private readonly _emailField: Locator
  private readonly _passwordField: Locator
  private readonly _loginButton: Locator
  constructor(page: Page) {
    this._page = page
    this._quickLoginButton = page.locator('[role="alert"] button:has-text("Login")')

    const formLocator = page.locator('form')
    this._emailField = formLocator.locator('input[name="email"]')
    this._passwordField = formLocator.locator('input[name="password"]')
    this._loginButton = formLocator.locator('button[type="submit"]')
  }

  @boxedStep
  async open() {
    await this._page.goto('/login')
    return this.loaded()
  }

  @boxedStep
  async loaded() {
    await expect(this._quickLoginButton).toBeVisible()
    await expect(this._emailField).toBeVisible()
    await expect(this._passwordField).toBeVisible()
    await expect(this._loginButton).toBeVisible()

    return this
  }

  get locator(): Locator {
    return this._page.locator('body')
  }

  get emailField(): Locator {
    return this._emailField
  }

  get passwordField(): Locator {
    return this._passwordField
  }
}
