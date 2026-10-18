import { test, expect } from '@playwright/test'

test.use({ locale: 'en-US' })

test('warns about self-XSS in the production browser console', async ({ page }) => {
  const messages: string[] = []
  page.on('console', (message) => {
    if (message.type() === 'log') {
      messages.push(message.text())
    }
  })

  await page.goto('/')

  // eslint-disable-next-line playwright/no-skipped-test -- Requires a production build.
  test.skip(
    (await page.locator('script[src="/@vite/client"]').count()) > 0,
    'The self-XSS warning is disabled on the Vite development server.'
  )

  await expect.poll(() => messages.join('\n')).toContain('Stop!')
  await expect
    .poll(() => messages.join('\n'))
    .toContain('browser console is meant for developers')
})
