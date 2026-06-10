// NuxtPrint

import { readFileSync } from 'fs'
import { expect, test } from '@playwright/test'
import { getPdfProperties } from '@/utils/getPdfProperties'
import { loginAndSetCookie, mockDateNow } from '@/utils/helpers'
import { CampItem, PeriodItem } from '@/shared-types/ecamp'

test.describe('Nuxt print test', () => {
  test.beforeEach(async ({ page, request }) => {
    await loginAndSetCookie(page, request, 'test@example.com')
    await mockDateNow(page)
  })

  test('shows print preview', async ({ page }) => {
    const campsResponse = await page.request.get('/api/camps.jsonhal')
    const body = (await campsResponse.json()) as {
      _embedded: { items: CampItem[] }
    }
    const camp = body._embedded.items.find((c) => c.motto)
    const campUri = camp!._links.self.href
    const campPeriodsLink = camp!._links.periods.href

    const periodsResponse = await page.request.get(campPeriodsLink)
    const periodsResponseBody = (await periodsResponse.json()) as {
      _embedded: { items: PeriodItem[] }
    }
    const period = periodsResponseBody._embedded.items[0]
    const periodUri = period._links.self.href

    const printConfig = {
      language: 'en',
      documentName: 'camp',
      options: { pageNumbers: false },
      camp: campUri,
      contents: [
        {
          type: 'Cover',
          options: {},
        },
        {
          type: 'Picasso',
          options: {
            periods: [periodUri],
            orientation: 'L',
          },
        },
        {
          type: 'Story',
          options: {
            periods: [periodUri],
            contentType: 'Storycontext',
          },
        },
        {
          type: 'Program',
          options: {
            periods: [periodUri],
            dayOverview: true,
          },
        },
        {
          type: 'Toc',
          options: {},
        },
      ],
    }

    const PRINT_URL = process.env.PRINT_URL || 'http://localhost:3000/print'
    await page.goto(
      `${PRINT_URL}/?config=${encodeURIComponent(JSON.stringify(printConfig))}`
    )
    await expect(page.locator('body')).toContainText(camp!.title)
    await expect(page.locator('body')).toContainText(camp!.motto!)

    await expect(page.locator('#content_0_cover')).toHaveCSS('font-size', '50px')
  })

  test.describe('downloads PDF', () => {
    test.beforeEach(async ({ page }) => {
      await page.goto('/camps')
      await page.locator('a:has-text("GRGR")').click()
    })

    test('for whole camp', async ({ page }) => {
      await page.locator('a:has-text("Admin")').click()
      await page.locator('a:has-text("Drucken")').click()

      const downloadPromise = page.waitForEvent('download')
      await page.locator('button:has-text("PDF herunterladen (Layout #1)")').click()
      const download = await downloadPromise

      const path = await download.path()
      const buffer = readFileSync(path)
      const pdfProps = await getPdfProperties(buffer)

      expect(download.suggestedFilename()).toBe('Pfila-2023.pdf')
      expect(pdfProps.numPages).toBe(25)
    })

    // eslint-disable-next-line playwright/no-skipped-test
    test.skip('for picasso', async ({ page }) => {
      await page.locator('a:has-text("Programm")').click()
      await page.locator('[data-testid="campprogram-menu"]').click()

      const downloadPromise = page.waitForEvent('download')
      await page.locator('listitem:has-text("PDF herunterladen (Layout #1)")').click()
      const download = await downloadPromise

      const path = await download.path()
      const buffer = readFileSync(path)
      const pdfProps = await getPdfProperties(buffer)

      expect(download.suggestedFilename()).toBe('Pfila-2023-Hauptlager.pdf')
      expect(pdfProps.numPages).toBe(1)
    })
  })
})
