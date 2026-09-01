import { test, expect } from '@playwright/test'

function formTestUrl(
  component: string,
  options: { props?: unknown; value?: unknown } = {}
) {
  const params = new URLSearchParams()
  if (options.props !== undefined) params.set('props', JSON.stringify(options.props))
  if (options.value !== undefined) params.set('value', JSON.stringify(options.value))
  const query = params.toString()
  return `/form-test/${component}${query ? `?${query}` : ''}`
}

test('lists the available form components', async ({ page }) => {
  await page.goto('/form-test')
  const list = page.getByTestId('form-test-unknown')
  await expect(list).toContainText('ETextField')
  await expect(list).toContainText('ECheckbox')
})

test('ETextField reflects typed input in its v-model', async ({ page }) => {
  await page.goto(formTestUrl('ETextField', { props: { label: 'Name' } }))

  const subject = page.getByTestId('form-test-subject')
  await expect(subject.locator('label').first()).toHaveText('Name')
  await expect(page.getByTestId('form-test-model')).toHaveText('null')

  await subject.locator('input').fill('Hello world')

  await expect(page.getByTestId('form-test-model')).toHaveText('"Hello world"')
})

test('ECheckbox toggles its boolean v-model', async ({ page }) => {
  await page.goto(formTestUrl('ECheckbox', { props: { label: 'Agree' }, value: false }))

  await expect(page.getByTestId('form-test-model')).toHaveText('false')

  await page.getByTestId('form-test-subject').locator('input[type="checkbox"]').click()

  await expect(page.getByTestId('form-test-model')).toHaveText('true')
})
