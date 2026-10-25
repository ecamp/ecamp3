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
    await expect(comment.locator('button.ec-comment-card__delete')).toBeVisible()
    await comment.locator('button.ec-comment-card__delete').click()
    await page.locator('.v-overlay--active button.bg-error').click()

    await expect(comment).toHaveCount(0)
  })

  test('submits with ctrl+enter without inserting a line break', async ({
    page,
    request,
    runId,
  }) => {
    const text = `Tastatur ${runId}`

    await loginAndSetCookie(page, request, bipiUser)
    await page.goto(`/camps/${skilagerCampId}/Skilager/program`)
    await page.locator('a[href*="/program/activity/"]').first().click()
    await page.getByTestId('comments-toggle').click()

    const composer = page.getByTestId('comments-panel').getByTestId('comment-composer')
    await composer.locator('.ProseMirror').click()
    await composer.locator('.ProseMirror').pressSequentially(text, { delay: 30 })
    await page.keyboard.press('ControlOrMeta+Enter')

    const comment = page.getByTestId('comment-card').filter({ hasText: text })
    await expect(comment).toBeVisible()

    await expect(comment.locator('.ProseMirror')).toHaveText(text)
    expect(await comment.locator('.ProseMirror').innerHTML()).not.toContain('<br')

    await comment.hover()
    await expect(comment.locator('button.ec-comment-card__delete')).toBeVisible()
    await comment.locator('button.ec-comment-card__delete').click()
    await page.locator('.v-overlay--active button.bg-error').click()

    await expect(comment).toHaveCount(0)
  })

  test('replies to, resolves and reopens a thread', async ({ page, request, runId }) => {
    const text = `Thread ${runId}`
    const replyText = `Antwort ${runId}`

    await loginAndSetCookie(page, request, bipiUser)
    await page.goto(`/camps/${skilagerCampId}/Skilager/program`)
    await page.locator('a[href*="/program/activity/"]').first().click()
    await page.getByTestId('comments-toggle').click()
    const panel = page.getByTestId('comments-panel')

    const composer = panel.getByTestId('comment-composer').last()
    await composer.locator('.ProseMirror').click()
    await composer.locator('.ProseMirror').pressSequentially(text, { delay: 30 })
    // any further keydown makes ProseMirror read in the last typed character
    await page.keyboard.press('End')
    await composer.getByTestId('comment-submit').click()

    const thread = panel.getByTestId('comment-thread').filter({ hasText: text })
    await expect(thread).toBeVisible()

    await thread.getByTestId('comment-thread-reply').click()
    const replyEditor = thread
      .getByTestId('comment-composer')
      .locator('.ProseMirror[contenteditable="true"]')
    await expect(replyEditor).toBeVisible()
    await replyEditor.click()
    await replyEditor.pressSequentially(replyText, { delay: 30 })
    await page.keyboard.press('End')
    await thread.getByTestId('comment-submit').click()

    const cards = thread.getByTestId('comment-card')
    await expect(cards).toHaveCount(2)
    await expect(cards.last()).toContainText(replyText)
    // only own replies, so the thread may still be deleted as a whole
    await expect(cards.first().locator('button.ec-comment-card__delete')).toBeEnabled()

    await thread.getByTestId('comment-thread-resolve').click()
    await expect(thread).toHaveCount(0)

    await panel.getByTestId('comments-resolved-toggle').locator('.v-chip').last().click()
    await expect(thread).toBeVisible()
    await expect(thread.getByTestId('comment-card')).toHaveCount(2)
    await expect(thread.getByTestId('comment-thread-resolved')).toBeVisible()

    await thread.getByTestId('comment-thread-resolve').click()
    await expect(thread.getByTestId('comment-thread-resolved')).toHaveCount(0)
    await panel.getByTestId('comments-resolved-toggle').locator('.v-chip').first().click()
    await expect(thread).toBeVisible()

    const reply = thread.getByTestId('comment-card').filter({ hasText: replyText })
    await reply.hover()
    await reply.locator('button.ec-comment-card__delete').click()
    await page.locator('.v-overlay--active button.bg-error').click()
    await expect(reply).toHaveCount(0)

    const root = thread.getByTestId('comment-card').first()
    await root.hover()
    await root.locator('button.ec-comment-card__delete').click()
    await page.locator('.v-overlay--active button.bg-error').click()
    await expect(thread).toHaveCount(0)
  })
})
