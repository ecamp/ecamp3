import { expect, test } from '@playwright/test'
import { bipiUser, grgrCampId } from '@/utils/constants'
import { loginAndSetCookie } from '@/utils/helpers'

const runId = Date.now()

test.describe('bulk invite collaborators', () => {
  test.describe.configure({ mode: 'serial' })

  test.beforeEach(async ({ page, request }) => {
    await loginAndSetCookie(page, request, bipiUser)
    await expect(page.getByTestId('create-camp-button')).toBeVisible({ timeout: 60000 })
    await page.goto(`/camps/${grgrCampId}/GRGR/admin/collaborators`)
    await expect(
      page.getByRole('button', { name: 'Mehrere Personen einladen' })
    ).toBeVisible({ timeout: 20000 })
  })

  test('opens dialog and shows form fields', { tag: '@mature' }, async ({ page }) => {
    await page.getByRole('button', { name: 'Mehrere Personen einladen' }).click()
    const overlay = page.locator('.v-overlay--active')
    await expect(overlay).toBeVisible()

    await expect(overlay.getByRole('textbox')).toBeVisible()
    await expect(overlay.getByRole('combobox', { name: /Rolle/ })).toBeVisible()
    const submitButton = overlay.getByRole('button', { name: 'Einladungen verschicken' })
    await expect(submitButton).toBeVisible()
    await expect(submitButton).toBeDisabled()
  })

  test(
    'invites new people and shows success count',
    { tag: '@mature' },
    async ({ page }) => {
      const email1 = `run${runId}-a@example.com`
      const email2 = `run${runId}-b@example.com`

      await page.getByRole('button', { name: 'Mehrere Personen einladen' }).click()
      const overlay = page.locator('.v-overlay--active')
      await expect(overlay).toBeVisible()

      await overlay.getByRole('textbox').fill(`${email1}\n${email2}`)
      await overlay.getByRole('button', { name: 'Einladungen verschicken' }).click()

      await expect(overlay.locator('.v-alert')).toContainText('2')

      await page.keyboard.press('Escape')
      await expect(overlay).toBeHidden()

      await expect(page.getByText(email1, { exact: true })).toBeVisible()
      await expect(page.getByText(email2, { exact: true })).toBeVisible()
    }
  )

  test(
    'reports already-invited email in the result',
    { tag: '@mature' },
    async ({ page }) => {
      const newEmail = `run${runId}-c@example.com`

      await page.getByRole('button', { name: 'Mehrere Personen einladen' }).click()
      const overlay = page.locator('.v-overlay--active')
      await expect(overlay).toBeVisible()

      await overlay.getByRole('textbox').fill(`x@z.com\n${newEmail}`)

      await overlay.getByRole('button', { name: 'Einladungen verschicken' }).click()

      await expect(
        overlay.getByRole('alert').filter({ hasText: 'x@z.com' })
      ).toBeVisible()
    }
  )

  test(
    'shows failed email in the result when invite fails',
    { tag: '@mature' },
    async ({ page }) => {
      const email1 = `run${runId}-d@example.com`
      const email2 = `run2${runId}-d@example.com`

      await page.getByRole('button', { name: 'Mehrere Personen einladen' }).click()
      const overlay = page.locator('.v-overlay--active')
      await expect(overlay).toBeVisible()

      await overlay.getByRole('textbox').fill(`${email1};${email2}`)

      await page.route('**/camp_collaborations', async (route) => {
        if (route.request().method() === 'POST') {
          await route.fulfill({ status: 500, body: 'Internal Server Error' })
        } else {
          await route.continue()
        }
      })

      await overlay.getByRole('button', { name: 'Einladungen verschicken' }).click()

      const failedAlert = overlay.getByRole('alert').filter({ hasText: email1 })
      await expect(failedAlert).toContainText(email1)
      await expect(failedAlert).toContainText(email2)

      await expect(overlay.getByRole('textbox', { name: 'E-Mail-Adressen' })).toHaveValue(
        `${email1};${email2}`
      )
    }
  )

  test(
    'closes dialog and resets form on cancel',
    { tag: '@mature' },
    async ({ page }) => {
      await page.getByRole('button', { name: 'Mehrere Personen einladen' }).click()
      const overlay = page.locator('.v-overlay--active')
      await expect(overlay).toBeVisible()

      await overlay.getByRole('textbox').fill('test@example.com')

      await page.keyboard.press('Escape')
      await expect(overlay).toBeHidden()

      await page.getByRole('button', { name: 'Mehrere Personen einladen' }).click()
      await expect(page.locator('.v-overlay--active').getByRole('textbox')).toHaveValue(
        ''
      )
    }
  )
})
