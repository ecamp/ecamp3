import {
  expect,
  request,
  test,
  type APIRequestContext,
  type Page,
} from '@playwright/test'
export const API_ROOT_URL = process.env.API_ROOT_URL || 'http://localhost:3000/api'
export const API_ROOT_URL_CACHED =
  process.env.API_ROOT_URL_CACHED || 'http://localhost:3004'

export async function login(
  request: APIRequestContext,
  identifier: string,
  password: string = 'test'
) {
  const response = await request.post(`${API_ROOT_URL}/authentication_token`, {
    data: { identifier, password },
  })
  expect([204]).toContain(response.status())
}

export async function loginAndSetCookie(
  page: Page,
  _: unknown,
  user: string,
  password: string = 'test'
) {
  await page.goto('/')
  await page.locator('[type="email"]').fill(user)
  await page.locator('[type="password"]').fill(password)
  await Promise.all([
    page.locator('[type="submit"]').click(),
    page.waitForURL('/camps', { timeout: 60000 }),
  ])
}

export async function getAuthContext(user: string): Promise<APIRequestContext> {
  const defaultRequest = await request.newContext()
  await login(defaultRequest, user)
  const state = await defaultRequest.storageState()
  await defaultRequest.dispose()
  return await request.newContext({
    storageState: state,
  })
}

export { getPdfProperties } from './getPdfProperties'

export async function expectCacheHeader(
  request: APIRequestContext,
  uri: string,
  expectedHeader: string
) {
  await test.step(
    `Check Header: ${expectedHeader}`,
    async () => {
      const response = await request.get(`${API_ROOT_URL_CACHED}${uri}.jsonhal`)
      expect(response.headers()['x-cache']).toBe(expectedHeader)
    },
    { box: true }
  )
}

export async function expectCacheHit(request: APIRequestContext, uri: string) {
  await test.step(
    'Expect Cache HIT',
    async () => {
      await expectCacheHeader(request, uri, 'HIT')
    },
    { box: true }
  )
}

export async function expectCacheMiss(request: APIRequestContext, uri: string) {
  await test.step(
    'Expect Cache MISS',
    async () => {
      await expectCacheHeader(request, uri, 'MISS')
    },
    { box: true }
  )
}

export async function expectCachePass(request: APIRequestContext, uri: string) {
  await test.step(
    'Expect Cache PASS',
    async () => {
      await expectCacheHeader(request, uri, 'PASS')
    },
    { box: true }
  )
}

export async function waitForCacheMiss(request: APIRequestContext, uri: string) {
  await test.step(
    `Wait for Cache MISS on ${uri}`,
    async () => {
      await expect
        .poll(
          async () => {
            const response = await request.get(`${API_ROOT_URL_CACHED}${uri}.jsonhal`)
            return response.headers()['x-cache']
          },
          { timeout: 10000 }
        )
        .toBe('MISS')
    },
    { box: true }
  )
}

export async function apiGet(request: APIRequestContext, uri: string) {
  return await request.get(`${API_ROOT_URL_CACHED}${uri}.jsonhal`)
}

export async function apiPatch(
  request: APIRequestContext,
  uri: string,
  body: Record<string, unknown>
) {
  return await request.patch(`${API_ROOT_URL_CACHED}${uri}.jsonhal`, {
    data: body,
    headers: {
      'Content-Type': 'application/merge-patch+json',
    },
  })
}

export async function apiPost(
  request: APIRequestContext,
  uri: string,
  body: Record<string, unknown>
) {
  return await request.post(`${API_ROOT_URL_CACHED}${uri}.jsonhal`, {
    data: body,
    headers: {
      'Content-Type': 'application/hal+json',
    },
  })
}

export async function apiDelete(request: APIRequestContext, uri: string) {
  return await request.delete(`${API_ROOT_URL_CACHED}${uri}.jsonhal`)
}

export async function createCampViaUI(page: Page, campTitle: string): Promise<string> {
  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)
  const in2Days = new Date()
  in2Days.setDate(in2Days.getDate() + 2)

  await page.goto('/camps')
  const createCampButton = page.getByTestId('create-camp-button')
  await expect(createCampButton).toBeVisible({ timeout: 10_000 })
  await createCampButton.click()

  await page.locator('[data-testid="create-camp-title-input"] input').fill(campTitle)
  await page
    .locator('[data-testid="start-date-picker"] input')
    .fill(tomorrow.toLocaleDateString('de-CH'))
  await page
    .locator('[data-testid="end-date-picker"] input')
    .fill(in2Days.toLocaleDateString('de-CH'))

  await page.getByTestId('create-camp-next-step').click()

  const prototypeSelect = page.locator('div.v-input[data-testid="prototype-select"]')
  await expect(prototypeSelect).toBeVisible({ timeout: 10_000 })
  await prototypeSelect.click()
  await expect(page.locator('.v-overlay--active')).toBeVisible({ timeout: 10000 })
  await page.locator('.v-overlay--active').getByText('Keine Vorlage').click()
  await expect(
    page.getByText('Achtung: Du hast "Keine Vorlage" ausgewählt.')
  ).toBeVisible()
  await expect(page.locator('.v-overlay')).not.toBeVisible({ timeout: 10000 })

  await page.getByTestId('create-camp-button').click()
  await page.waitForURL('**/admin/info', { timeout: 30000 })

  return page.url().replace(/\/info$/, '')
}

export async function deleteCampViaUI(
  page: Page,
  campAdminBaseUrl: string,
  campTitle: string
): Promise<void> {
  await page.goto(`${campAdminBaseUrl}/info`)

  await page
    .locator('.v-expansion-panel')
    .filter({ hasText: 'Gefahrenzone' })
    .locator('.v-expansion-panel-title')
    .click()

  await page
    .locator('.v-expansion-panel-text')
    .getByRole('button', { name: /Löschen/i })
    .click()

  await page.locator('[name="promptText"] input').fill(campTitle)

  await page
    .locator('.v-overlay--active')
    .getByRole('button', { name: /Löschen/i })
    .click()

  await page.waitForURL(/\/camps$/, { timeout: 15000 })
}
