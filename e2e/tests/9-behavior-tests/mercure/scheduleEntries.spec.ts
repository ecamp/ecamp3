import { test, expect, type APIRequestContext } from '@playwright/test'
import {
  bipiUser,
  felicitySmoakUser,
  grgrPeriodId,
  skilagerPeriodId,
} from '@/utils/constants'
import { API_ROOT_URL, apiDelete, apiPost, getAuthContext } from '@/utils/helpers'

const MERCURE_URL = process.env.MERCURE_URL || 'http://localhost:3020/.well-known/mercure'

const collectionTopic = `/api/periods/${grgrPeriodId}/schedule_entries`

async function requestToken(api: APIRequestContext, topic: string) {
  return await api.post(`${API_ROOT_URL}/mercure/token`, { data: { topic } })
}

async function getToken(api: APIRequestContext, topic: string): Promise<string> {
  const response = await requestToken(api, topic)
  expect(response.status()).toBe(200)
  return (await response.json()).token
}

type MercureUpdate = { op: string; path?: string; value?: string }

/**
 * Subscribes to a topic and collects all updates until the returned stop function is called.
 */
async function subscribe(topic: string, token?: string) {
  const abortController = new AbortController()
  const url = new URL(MERCURE_URL)
  url.searchParams.append('match', topic)
  const response = await fetch(url, {
    signal: abortController.signal,
    headers: token ? { Authorization: `Bearer ${token}` } : {},
  })

  const updates: MercureUpdate[] = []
  if (response.ok && response.body) {
    void (async () => {
      const decoder = new TextDecoder()
      let buffer = ''
      try {
        for await (const chunk of response.body as unknown as AsyncIterable<Uint8Array>) {
          buffer += decoder.decode(chunk, { stream: true })
          const events = buffer.split(/\r?\n\r?\n/)
          buffer = events.pop() ?? ''
          for (const event of events) {
            const data = event
              .split(/\r?\n/)
              .filter((line) => line.startsWith('data:'))
              .map((line) => line.slice(5).trimStart())
              .join('\n')
            if (data) updates.push(JSON.parse(data))
          }
        }
      } catch {
        // aborted by stop()
      }
    })()
  }

  return { status: response.status, updates, stop: () => abortController.abort() }
}

test.describe('mercure: /periods/{periodId}/schedule_entries', { tag: '@mature' }, () => {
  test.describe.configure({ mode: 'serial' })

  test('issues no token to anonymous users', async ({ request }) => {
    const response = await requestToken(request, collectionTopic)
    expect(response.status()).toBe(401)
  })

  test('issues no token to users without access to the camp', async () => {
    const felicityApi = await getAuthContext(felicitySmoakUser)
    const response = await requestToken(felicityApi, collectionTopic)
    expect([403, 404]).toContain(response.status())
  })

  test('rejects subscription without token', async () => {
    const subscription = await subscribe(collectionTopic)
    subscription.stop()
    expect(subscription.status).toBe(401)
  })

  test('does not deliver updates to subscribers with a token for another topic', async () => {
    const bipiApi = await getAuthContext(bipiUser)
    const validToken = await getToken(bipiApi, collectionTopic)
    // The hub accepts the connection, but only delivers private updates matching the token's topics
    const otherToken = await getToken(
      bipiApi,
      `/api/periods/${skilagerPeriodId}/schedule_entries`
    )

    const valid = await subscribe(collectionTopic, validToken)
    const other = await subscribe(collectionTopic, otherToken)

    try {
      const postRes = await apiPost(bipiApi, '/api/schedule_entries', {
        activity: '/activities/ffd08c52288c',
        end: '2036-05-11T06:00:00+00:00',
        period: `/periods/${grgrPeriodId}`,
        start: '2036-05-11T05:00:00+00:00',
      })
      expect(postRes.status()).toBe(201)

      // once the authorized subscriber has received the update, the other one would have received it too
      await expect.poll(() => valid.updates.length).toBeGreaterThan(0)
      expect(other.updates).toEqual([])

      await apiDelete(bipiApi, (await postRes.json())._links.self.href)
      await expect.poll(() => valid.updates.map((u) => u.op)).toContain('remove')
      expect(other.updates).toEqual([])
    } finally {
      valid.stop()
      other.stop()
    }
  })

  test('publishes creation and deletion of a schedule entry to subscribers', async () => {
    const bipiApi = await getAuthContext(bipiUser)
    const token = await getToken(bipiApi, collectionTopic)

    const subscription = await subscribe(collectionTopic, token)
    expect(subscription.status).toBe(200)

    try {
      const postRes = await apiPost(bipiApi, '/api/schedule_entries', {
        activity: '/activities/ffd08c52288c',
        end: '2036-05-12T06:00:00+00:00',
        period: `/periods/${grgrPeriodId}`,
        start: '2036-05-12T05:00:00+00:00',
      })
      expect(postRes.status()).toBe(201)
      const newScheduleEntryUri: string = (await postRes.json())._links.self.href

      await expect
        .poll(() => subscription.updates.map((update) => update.op))
        .toContain('add')
      const created = subscription.updates.find((update) => update.op === 'add')
      expect(JSON.parse(created?.value ?? '{}')._links.self.href).toBe(
        newScheduleEntryUri
      )

      await apiDelete(bipiApi, newScheduleEntryUri)

      await expect
        .poll(() => subscription.updates.map((update) => update.op))
        .toContain('remove')
      const removed = subscription.updates.find((update) => update.op === 'remove')
      expect(removed?.path).toContain(newScheduleEntryUri.replace(/^\/api/, ''))
    } finally {
      subscription.stop()
    }
  })
})
