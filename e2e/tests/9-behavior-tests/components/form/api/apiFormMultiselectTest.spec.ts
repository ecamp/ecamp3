import { test, expect, type Page } from '@playwright/test'
import { bipiUser } from '@/utils/constants'
import { getAuthContext, loginAndSetCookie, API_ROOT_URL } from '@/utils/helpers'

const SINGLETON_IRI = '/form_test_data/0123456789ab'

const props = {
  multiple: true,
  itemTitle: 'text',
  itemValue: 'value',
  items: [
    { value: 'en', text: 'English' },
    { value: 'de', text: 'Deutsch' },
    { value: 'fr', text: 'Français' },
    { value: 'it', text: 'Italiano' },
  ],
}

const URL = `/api-form-test/ApiSelect?path=languageMultiselect&props=${encodeURIComponent(
  JSON.stringify(props)
)}`

async function persistedValue(page: Page): Promise<string[]> {
  const text = (await page.getByTestId('api-form-test-value').innerText()).trim()
  return JSON.parse(text)
}

test('selecting multiple languages auto-saves the array and survives reload', async ({
  page,
}) => {
  await loginAndSetCookie(page, undefined, bipiUser)

  const api = await getAuthContext(bipiUser)
  const reset = await api.patch(`${API_ROOT_URL}${SINGLETON_IRI}`, {
    headers: { 'Content-Type': 'application/merge-patch+json' },
    data: { languageMultiselect: [] },
  })
  expect(reset.status()).toBe(200)

  await page.goto(URL)

  const subject = page.getByTestId('api-form-test-subject')
  await expect(subject).toBeVisible()
  await expect.poll(() => persistedValue(page)).toEqual([])

  await subject.locator('.v-field').click()
  await expect(page.getByRole('option', { name: 'English' })).toBeVisible()
  await page.getByRole('option', { name: 'English' }).click()
  await page.getByRole('option', { name: 'Français' }).click()

  await expect
    .poll(async () => (await persistedValue(page)).slice().sort(), { timeout: 15000 })
    .toEqual(['en', 'fr'])

  await page.keyboard.press('Escape')

  await page.getByTestId('api-form-test-reload').click()
  await expect
    .poll(async () => (await persistedValue(page)).slice().sort(), { timeout: 15000 })
    .toEqual(['en', 'fr'])

  await api.dispose()
})
