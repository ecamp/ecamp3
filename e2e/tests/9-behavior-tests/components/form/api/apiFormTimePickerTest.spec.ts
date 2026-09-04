import { test, expect, type Page } from '@playwright/test'
import { bipiUser } from '@/utils/constants'
import { getAuthContext, loginAndSetCookie, API_ROOT_URL } from '@/utils/helpers'

const SINGLETON_IRI = '/form_test_data/0123456789ab'

const SEED_TIME = '2024-01-15T09:30:00+00:00'

const URL = `/api-form-test/ApiTimePicker?path=time`

async function persistedValue(page: Page): Promise<string> {
  const text = (await page.getByTestId('api-form-test-value').innerText()).trim()
  return JSON.parse(text)
}

test('editing the time picker auto-saves the datetime and survives reload', async ({
  page,
}) => {
  await loginAndSetCookie(page, undefined, bipiUser)

  const api = await getAuthContext(bipiUser)
  const reset = await api.patch(`${API_ROOT_URL}${SINGLETON_IRI}`, {
    headers: { 'Content-Type': 'application/merge-patch+json' },
    data: { time: SEED_TIME },
  })
  expect(reset.status()).toBe(200)

  await page.goto(URL)

  const subject = page.getByTestId('api-form-test-subject')
  await expect(subject).toBeVisible()
  const input = subject.locator('input')
  await expect(input).toBeVisible()

  await expect.poll(() => persistedValue(page), { timeout: 15000 }).toBe(SEED_TIME)

  await input.fill('18:45')
  await input.press('Tab')

  await expect
    .poll(
      async () => {
        const value = await persistedValue(page)
        return typeof value === 'string' ? value : ''
      },
      { timeout: 15000 }
    )
    .toMatch(/^2024-01-15T18:45:00/)

  await page.getByTestId('api-form-test-reload').click()
  await expect
    .poll(
      async () => {
        const value = await persistedValue(page)
        return typeof value === 'string' ? value : ''
      },
      { timeout: 15000 }
    )
    .toMatch(/^2024-01-15T18:45:00/)

  await api.dispose()
})
