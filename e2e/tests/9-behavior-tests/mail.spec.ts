import { test } from '@/utils/etest'
import { expect } from '@playwright/test'

test('mail service responds with 200', { tag: '@mature' }, async ({ page }) => {
  await expect(await page.request.get('/mail')).toBeOK()
})
