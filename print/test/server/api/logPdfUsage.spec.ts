import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { useH3TestUtils } from '~/test/nitroH3/h3TestUtils'
import { createMockH3Event } from '~/test/nitroH3/mockH3Event'

describe('route: /api/logPdfUsage', () => {
  beforeEach(() => {
    useH3TestUtils()
    vi.stubGlobal('console', { log: vi.fn() })
  })
  afterEach(() => {
    vi.resetAllMocks()
  })

  it('returns 204', async () => {
    const handler = await import('~/server/api/logPdfUsage')

    const result = await handler.default({} as never)

    expect(result).toStrictEqual({ status: 204 })
    expect(console.log).toHaveBeenCalledWith(
      expect.toSatisfy(
        (log) => log.includes('"type":"clientPrint"') && log.includes('"status":204')
      )
    )
  })

  it('logs the print config when passed', async () => {
    const handler = await import('~/server/api/logPdfUsage')

    await handler.default(
      createMockH3Event({
        query: {
          config: printConfig,
        },
      })
    )
    expect(console.log).toHaveBeenCalledWith(expect.stringContaining(printConfig))
  })

  it('returns 400 for invalid json', async () => {
    const handler = await import('~/server/api/logPdfUsage')

    await handler.default(
      createMockH3Event({
        query: {
          config: printConfig.substring(0, printConfig.length - 5),
        },
      })
    )
    expect(console.log).toHaveBeenCalledWith(
      expect.toSatisfy(
        (log) => log.includes('"type":"clientPrint"') && log.includes('"status":400')
      )
    )
  })
})

const printConfig = JSON.stringify({
  language: 'en',
  documentName: 'camp',
  options: { pageNumbers: false },
  camp: '/api/camps/12873crh',
  contents: [
    {
      type: 'Cover',
      options: {},
    },
  ],
})
