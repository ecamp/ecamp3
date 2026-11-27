import { afterEach, describe, expect, it, vi } from 'vitest'
import { render } from '@/pdf/index.js'
import { renderPdf } from '../renderPdf.js'
import { insertPdfPages } from '../insertPdfPages.js'

vi.mock('@/pdf/index.js', () => ({ render: vi.fn(), prepare: null }))
vi.mock('../insertPdfPages.js', () => ({ insertPdfPages: vi.fn() }))
vi.mock('../i18n.js', () => ({ default: () => ({ translate: vi.fn() }) }))
vi.mock('../minimalHalJsonVuex.js', () => ({ default: () => ({ get: vi.fn() }) }))

describe('renderPdf', () => {
  afterEach(() => {
    vi.resetAllMocks()
  })

  const data = {
    config: { documentName: 'Pfila 2023', camp: '/camps/1a2b3c4d' },
    storeData: { lang: { language: 'de' }, api: {} },
    translationData: {},
  }
  const renderedBlob = new Blob(['rendered'])
  const insertedPage = { assetId: 'asset-1', pageIndex: 0 }

  function renderRegistering(insertedPages) {
    render.mockImplementation((props) => ({
      toBlob: async () => {
        Object.assign(props.insertedPages, insertedPages)
        return renderedBlob
      },
    }))
  }

  it('draws the inserted pages onto the rendered PDF', async () => {
    renderRegistering({ 3: insertedPage })
    const finalBlob = new Blob(['final'])
    insertPdfPages.mockResolvedValue(finalBlob)

    const result = await renderPdf(structuredClone(data), vi.fn())

    expect(insertPdfPages).toHaveBeenCalledWith(renderedBlob, { 3: insertedPage })
    expect(result.blob).toBe(finalBlob)
    expect(result.error).toBeNull()
  })

  it('returns the rendered PDF unchanged without inserted pages', async () => {
    renderRegistering({})

    const result = await renderPdf(structuredClone(data), vi.fn())

    expect(insertPdfPages).not.toHaveBeenCalled()
    expect(result.blob).toBe(renderedBlob)
  })

  it('reports an error when the pages cannot be inserted', async () => {
    renderRegistering({ 3: insertedPage })
    const error = new Error('An inserted PDF file is no longer available.')
    insertPdfPages.mockRejectedValue(error)

    const result = await renderPdf(structuredClone(data), vi.fn())

    expect(result.error).toBe(error)
    expect(result.blob).toBeNull()
  })
})
