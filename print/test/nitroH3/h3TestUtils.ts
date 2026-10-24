import type { H3Event } from 'h3'
import { vi } from 'vitest'

type Handler = () => Promise<unknown>

const h3 = {
  defineEventHandler: vi.fn((handler: Handler) => handler),
  readBody: vi.fn((event: H3Event) => event._requestBody || {}),
}

export function useH3TestUtils() {
  vi.stubGlobal('defineEventHandler', h3.defineEventHandler)
  vi.stubGlobal('readBody', h3.readBody)

  return h3
}
