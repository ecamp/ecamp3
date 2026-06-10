import { expectCacheHit, expectCacheMiss, loginAndSetCookie } from '@/utils/helpers'
import { test } from '@playwright/test'

const user1 = 'test@example.com'
const user2 = 'castor@example.com'

test('caches the root endpoint', async ({ browser }) => {
  const uri = '/api/index'

  // Create context for user 1
  const context1 = await browser.newContext()
  const page1 = await context1.newPage()
  await loginAndSetCookie(page1, context1, user1)

  await expectCacheMiss(context1.request, uri)
  await expectCacheHit(context1.request, uri)

  await context1.close()

  // Create context for user 2
  const context2 = await browser.newContext()
  const page2 = await context2.newPage()
  await loginAndSetCookie(page2, context2, user2)

  await expectCacheMiss(context2.request, uri)
  await context2.close()
})
