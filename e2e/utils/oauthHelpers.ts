import { type Page, type Browser } from '@playwright/test'

export type OAuthProvider = 'Google' | 'MiData' | 'CeviDB' | 'JublaDB'

export async function loginWithOAuth(
  page: Page,
  provider: OAuthProvider,
  username: string = 'test@example.com'
): Promise<void> {
  await page.goto('/login')
  await page.getByRole('button', { name: provider }).click()
  await page.waitForURL(/\/mock-auth\//, { timeout: 10_000 })
  await page.getByRole('button', { name: new RegExp(escapeRegExp(username)) }).click()
  await page.waitForURL('/camps', { timeout: 30_000 })
}

export async function withOAuthSession<T>(
  browser: Browser,
  provider: OAuthProvider,
  username: string,
  fn: (page: Page) => Promise<T>
): Promise<T> {
  const ctx = await browser.newContext()
  const page = await ctx.newPage()
  try {
    await loginWithOAuth(page, provider, username)
    return await fn(page)
  } finally {
    await ctx.close()
  }
}

export async function getProfileEmail(page: Page): Promise<string> {
  await page.goto('/profile')
  return page.locator('.e-profile--email input').inputValue()
}

function escapeRegExp(s: string): string {
  return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
}
