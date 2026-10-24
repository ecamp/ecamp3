import type { H3Event } from 'h3'
// @ts-expect-error we dont need types for this yet
import merge from 'lodash-es/merge'

export const createMockH3Event = (
  partialEvent: Partial<H3Event> & {
    body?: Record<string, never> | string
    params?: Record<string, never>
  }
): H3Event => {
  const event = {
    node: {
      req: {
        headers: { 'content-type': 'application/json' },
        method: 'POST',
      },
    },
    context: {
      params: partialEvent.params || {},
    },
    // Our mock readBody function will look for this property
    _requestBody: partialEvent.body,
  } as unknown as H3Event

  // Deeply merge the partial event to allow for overrides
  return merge(event, partialEvent) as H3Event
}
