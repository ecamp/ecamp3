import axios from 'axios'
import { fetchEventSource } from '@microsoft/fetch-event-source'
import { getEnv } from '@/environment.js'

// Separate from the axios instance of the API store, because the token endpoint is not a HAL resource
const axiosInstance = axios.create({
  withCredentials: true,
  baseURL: getEnv().API_ROOT_URL,
})

/**
 * Subscribes to updates of a resource (or collection) through Mercure.
 *
 * The hub only accepts subscribers with a token for exactly this topic. The token is requested from the API,
 * which only issues it if the current user may read the resource. A new token is requested on every
 * (re)connect, because the hub only checks the token when the connection is established.
 *
 * @param {string} topic IRI of the resource as seen by the browser, e.g. '/api/schedule_entries/1a2b3c'
 * @param {function(object): void} onMessage called with the parsed JSON data of every update
 * @returns {function(): void} function to end the subscription
 */
export function subscribeToMercureTopic(topic, onMessage) {
  const abortController = new AbortController()
  let lastEventId = null

  const url = new URL(getEnv().MERCURE_URL, window.location.href)
  // Mercure protocol 1.0: exact topic match
  url.searchParams.append('match', topic)

  fetchEventSource(url.toString(), {
    signal: abortController.signal,
    // keep the connection open when the tab is in the background
    openWhenHidden: true,
    async onopen(response) {
      if (!response.ok) {
        throw new FatalMercureError(`Mercure hub answered with status ${response.status}`)
      }
    },
    onmessage(event) {
      if (event.id) lastEventId = event.id
      if (event.data) onMessage(JSON.parse(event.data))
    },
    onerror(error) {
      // Fatal errors stop the subscription, anything else (e.g. network problems) is retried
      if (error instanceof FatalMercureError) throw error
    },
    // fetchEventSource has no hook to refresh headers on reconnect, so the token is added to every request here
    fetch: async (input, init) => {
      const token = await requestToken(topic)
      // Mercure protocol 1.0 resumes after a reconnect with a query parameter, not with the Last-Event-ID header
      if (lastEventId) {
        const resumeUrl = new URL(input)
        resumeUrl.searchParams.set('last_event_id', lastEventId)
        input = resumeUrl.toString()
      }
      return window.fetch(input, {
        ...init,
        headers: { ...init.headers, Authorization: `Bearer ${token}` },
      })
    },
  })

  return () => abortController.abort()
}

async function requestToken(topic) {
  try {
    const response = await axiosInstance.post('/mercure/token', { topic })
    return response.data.token
  } catch (error) {
    // Not allowed or not existing: retrying will not help
    if (error.response?.status >= 400 && error.response.status < 500) {
      throw new FatalMercureError(
        `Could not get Mercure token (${error.response.status})`
      )
    }
    throw error
  }
}

class FatalMercureError extends Error {}
