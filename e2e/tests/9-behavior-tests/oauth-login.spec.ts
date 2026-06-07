import { test, expect } from '@playwright/test'
import {
  loginWithOAuth,
  withOAuthSession,
  getProfileEmail,
  type OAuthProvider,
} from '@/utils/oauthHelpers'

test.describe('OAuth login', () => {
  const providers: OAuthProvider[] = ['Google', 'MiData', 'CeviDB', 'JublaDB']

  for (const provider of providers) {
    test(`${provider}: shows camp list after login`, async ({ page }) => {
      await loginWithOAuth(page, provider, 'test@example.com')
      await expect(page).toHaveURL('/camps')
    })
  }
})

test.describe('OAuth login – account re-use', () => {
  test('Google: second login returns the same account', async ({ browser }) => {
    const email1 = await withOAuthSession(
      browser,
      'Google',
      'admin@example.com',
      getProfileEmail
    )
    const email2 = await withOAuthSession(
      browser,
      'Google',
      'admin@example.com',
      getProfileEmail
    )
    expect(email2).toBe(email1)
  })
})

test.describe('OAuth login – cross-provider account linking', () => {
  test('Google and MiData with the same email link to the same account', async ({
    browser,
  }) => {
    const email1 = await withOAuthSession(
      browser,
      'Google',
      'test2@example.com',
      getProfileEmail
    )
    const email2 = await withOAuthSession(
      browser,
      'MiData',
      'test2@example.com',
      getProfileEmail
    )
    expect(email2).toBe(email1)
  })

  test('CeviDB and JublaDB with the same email link to the same account', async ({
    browser,
  }) => {
    const email1 = await withOAuthSession(
      browser,
      'CeviDB',
      'test2@example.com',
      getProfileEmail
    )
    const email2 = await withOAuthSession(
      browser,
      'JublaDB',
      'test2@example.com',
      getProfileEmail
    )
    expect(email2).toBe(email1)
  })
})

test.describe('OAuth login – separate users', () => {
  test('two different usernames produce two different accounts', async ({ browser }) => {
    const email1 = await withOAuthSession(
      browser,
      'Google',
      'test@example.com',
      getProfileEmail
    )
    const email2 = await withOAuthSession(
      browser,
      'Google',
      'test2@example.com',
      getProfileEmail
    )
    expect(email1).not.toBe(email2)
  })
})
