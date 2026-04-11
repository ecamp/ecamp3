import type { H3Event } from 'h3'
// @ts-expect-error we dont need types for this yet
import merge from 'lodash-es/merge'

export const createMockH3Event = (
  partialEvent: Partial<H3Event> & {
    params?: Record<string, never>
    query?: Record<string, never>
  }
): H3Event => {
  const event = {
    node: {
      req: {
        method: 'GET',
      },
    },
    context: {
      params: partialEvent.params || {},
      query: partialEvent.query || {},
    },
  } as unknown as H3Event

  // Deeply merge the partial event to allow for overrides
  return merge(event, partialEvent) as H3Event
}
