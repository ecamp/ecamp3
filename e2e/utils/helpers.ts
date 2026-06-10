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

export async function mockDateNow(page: Page, date: string = 'April 30 2026 13:00:00') {
  const fakeNow = new Date(date).valueOf()

  await page.addInitScript(`{
    Date = class extends Date {
      constructor(...args) {
        if (args.length === 0) {
          super(${fakeNow});
        } else {
          super(...args);
        }
      }
    }

    const __DateNowOffset = ${fakeNow} - Date.now();
    const __DateNow = Date.now;
    Date.now = () => __DateNow() + __DateNowOffset;
  }`)
}
