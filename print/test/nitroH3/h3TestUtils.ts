import type { H3Event } from 'h3'
import { vi } from 'vitest'

type Handler = () => Promise<unknown>

const h3 = {
  defineEventHandler: vi.fn((handler: Handler) => handler),
  getQuery: vi.fn((event: H3Event) => event.context?.query || {}),
}

export function useH3TestUtils() {
  vi.stubGlobal('defineEventHandler', h3.defineEventHandler)
  vi.stubGlobal('getQuery', h3.getQuery)

  return h3
}
