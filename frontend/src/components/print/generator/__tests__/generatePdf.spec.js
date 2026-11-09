import { afterEach, describe, expect, it, vi } from 'vitest'
import { prepareInMainThread } from '@/pdf/prepareInMainThread.js'
import { generatePdf } from '../generatePdf.js'

const worker = vi.hoisted(() => ({
  terminate: vi.fn(),
  render: vi.fn(),
}))

vi.mock('@/pdf/prepareInMainThread.js', () => ({ prepareInMainThread: vi.fn() }))
vi.mock('vite-plugin-comlink/symbol', () => {
  const endpointSymbol = Symbol('endpoint')
  return {
    endpointSymbol,
    wrap: () => ({
      [endpointSymbol]: { terminate: worker.terminate },
      renderPdfInWorker: worker.render,
    }),
  }
})

describe('generatePdf cancellation', () => {
  afterEach(() => {
    vi.unstubAllGlobals()
    vi.resetAllMocks()
  })

  const data = {
    config: { contents: [] },
    storeData: {},
    translationData: {},
    renderInWorker: true,
  }

  it('does not start a worker when cancelled while loading data', async () => {
    let finishPreparing
    prepareInMainThread.mockReturnValue(
      new Promise((resolve) => (finishPreparing = resolve))
    )
    const controller = new AbortController()
    const result = generatePdf(data, vi.fn(), controller.signal)
    controller.abort()
    finishPreparing()
    await expect(result).rejects.toMatchObject({ name: 'AbortError' })
    expect(worker.render).not.toHaveBeenCalled()
  })

  it('terminates the worker and settles the request when cancelled during rendering', async () => {
    vi.stubGlobal('Worker', class {})
    prepareInMainThread.mockResolvedValue()
    worker.render.mockReturnValue(new Promise(() => {}))
    const controller = new AbortController()
    const result = generatePdf(data, vi.fn(), controller.signal)
    await vi.waitFor(() => expect(worker.render).toHaveBeenCalled())
    controller.abort()
    await expect(result).rejects.toMatchObject({ name: 'AbortError' })
    expect(worker.terminate).toHaveBeenCalledOnce()
  })
})
