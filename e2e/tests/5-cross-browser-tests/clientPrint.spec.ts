// ClientPrint

import { test, expect } from '@playwright/test'
import { getPdfProperties } from '@/utils/getPdfProperties'
import { loginAndSetCookie } from '@/utils/helpers'

import { readFileSync } from 'fs'

test.describe('Client print test', { tag: '@mature' }, () => {
  test.beforeEach(async ({ page, request }) => {
    await loginAndSetCookie(page, request, 'test@example.com')
  })

  test('downloads PDF', async ({ page }) => {
    await page.goto('/')
    await page.waitForURL('/camps')

    await page.locator('a:has-text("GRGR")').click()
    await page.locator('a:has-text("Admin")').click()
    await page.locator('a:has-text("Drucken")').click()

    const downloadPromise = page.waitForEvent('download')
    await page.locator('button:has-text("PDF herunterladen (Layout #2)")').click()
    const download = await downloadPromise

    const path = await download.path()
    const buffer = readFileSync(path)
    const pdfProps = await getPdfProperties(buffer)

    expect(download.suggestedFilename()).toBe('Pfila-2023.pdf')
    expect(pdfProps.numPages).toBe(20)
  })
})
