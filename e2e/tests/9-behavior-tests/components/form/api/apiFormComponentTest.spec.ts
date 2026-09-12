import { test, expect } from '@playwright/test'
import { bipiUser } from '@/utils/constants'
import { loginAndSetCookie } from '@/utils/helpers'

const URL = '/api-form-test/ApiTextField?path=text'

test('a change in one tab is visible in another tab after reload', async ({
  page,
  context,
  request,
}) => {
  await loginAndSetCookie(page, request, bipiUser)

  const tabA = page
  const tabB = await context.newPage()

  await tabA.goto(URL)
  await tabB.goto(URL)

  const inputA = tabA.getByTestId('api-form-test-subject').locator('input')
  const valueA = tabA.getByTestId('api-form-test-value')
  const valueB = tabB.getByTestId('api-form-test-value')

  await expect(inputA).toBeVisible()
  await expect(tabB.getByTestId('api-form-test-subject').locator('input')).toBeVisible()

  const before = (await valueB.innerText()).trim()
  const newValue = `cross-tab ${Date.now()}`
  const newValueJson = JSON.stringify(newValue)
  expect(before).not.toBe(newValueJson)

  await inputA.fill(newValue)
  await inputA.press('Tab')
  await expect(valueA).toHaveText(newValueJson, { timeout: 15000 })

  await expect(valueB).toHaveText(before)

  await tabB.getByTestId('api-form-test-reload').click()
  await expect(valueB).toHaveText(newValueJson, { timeout: 15000 })
})
