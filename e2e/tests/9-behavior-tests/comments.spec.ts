import { expect } from '@playwright/test'
import { test } from '@/utils/etest'
import { bipiUser, skilagerCampId } from '@/utils/constants'
import { loginAndSetCookie } from '@/utils/helpers'

test.describe('comments on an activity', () => {
  test('writes, reads and deletes an own comment', async ({ page, request, runId }) => {
    const text = `Kommentar ${runId}`

    await loginAndSetCookie(page, request, bipiUser)
    await page.goto(`/camps/${skilagerCampId}/Skilager/program`)
    await page.locator('a[href*="/program/activity/"]').first().click()
    await expect(page.getByTestId('comments-toggle')).toBeVisible()

    await page.getByTestId('comments-toggle').click()
    const panel = page.getByTestId('comments-panel')
    await expect(panel).toBeVisible()

    const composer = panel.getByTestId('comment-composer')
    await composer.locator('.ProseMirror').click()
    await composer.locator('.ProseMirror').pressSequentially(text, { delay: 30 })
    await composer.getByTestId('comment-submit').click()

    const comment = page.getByTestId('comment-card').filter({ hasText: text })
    await expect(comment).toBeVisible()

    await page.reload()
    await page.getByTestId('comments-toggle').click()
    await expect(comment).toBeVisible()

    await comment.hover()
    await comment.locator('button.ec-comment-card__delete').click()
    await page.locator('.v-overlay--active button.bg-error').click()

    await expect(comment).toHaveCount(0)
  })
})
