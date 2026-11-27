import { beforeEach, describe, expect, it, vi } from 'vitest'
import { PDFArray, PDFDocument, PDFRawStream, decodePDFRawStream } from 'pdf-lib'
import { insertPdfPages } from '../insertPdfPages.js'
import { loadAsset } from '../assetStore.js'

vi.mock('../assetStore.js', () => ({ loadAsset: vi.fn() }))

async function createPdf(pageSizes) {
  const pdf = await PDFDocument.create()
  pageSizes.forEach((size) => pdf.addPage(size).drawText('x'))
  return new Blob([await pdf.save()], { type: 'application/pdf' })
}

async function contentStreamsOfPage(blob, pageIndex) {
  const pdf = await PDFDocument.load(await blob.arrayBuffer())
  const contents = pdf.getPage(pageIndex).node.Contents()
  const streams = contents instanceof PDFArray ? contents.asArray() : [contents]
  return streams.map((ref) => {
    const stream = pdf.context.lookup(ref)
    const bytes =
      stream instanceof PDFRawStream
        ? decodePDFRawStream(stream).decode()
        : stream.getContents()
    return new TextDecoder().decode(bytes)
  })
}

describe('insertPdfPages', () => {
  beforeEach(() => {
    loadAsset.mockReset()
  })

  it('draws the selected page centered and scaled to fit below the rendered content', async () => {
    const rendered = await createPdf([
      [200, 200],
      [200, 200],
    ])
    loadAsset.mockResolvedValue(
      await createPdf([
        [100, 100],
        [100, 400],
      ])
    )

    const result = await insertPdfPages(rendered, {
      2: { assetId: 'asset-1', pageIndex: 1 },
    })

    expect(loadAsset).toHaveBeenCalledWith('asset-1')
    expect(await contentStreamsOfPage(result, 0)).toHaveLength(1)
    const [inserted, ...renderedContent] = await contentStreamsOfPage(result, 1)
    expect(inserted).toContain('1 0 0 1 75 0 cm')
    expect(inserted).toContain('0.5 0 0 0.5 0 0 cm')
    expect(inserted).toMatch(/\/EmbeddedPdfPage-\d+ Do/)
    expect(renderedContent.join('\n')).toContain('Tj')
  })

  it('loads each file only once', async () => {
    const rendered = await createPdf([
      [200, 200],
      [200, 200],
    ])
    loadAsset.mockResolvedValue(await createPdf([[100, 100]]))

    await insertPdfPages(rendered, {
      1: { assetId: 'asset-1', pageIndex: 0 },
      2: { assetId: 'asset-1', pageIndex: 0 },
    })

    expect(loadAsset).toHaveBeenCalledTimes(1)
  })

  it('fails when the file is no longer stored', async () => {
    const rendered = await createPdf([[200, 200]])
    loadAsset.mockResolvedValue(undefined)

    await expect(
      insertPdfPages(rendered, { 1: { assetId: 'missing', pageIndex: 0 } })
    ).rejects.toThrow('An inserted PDF file is no longer available.')
  })
})
