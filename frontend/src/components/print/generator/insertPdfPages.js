import { PDFArray, PDFDocument } from 'pdf-lib'
import { loadAsset } from './assetStore.js'

/**
 * Draws pages of previously uploaded PDF files onto pages of the rendered PDF.
 *
 * @param blob the rendered PDF
 * @param insertedPages object mapping page numbers of the rendered PDF to { assetId, pageIndex }
 */
export async function insertPdfPages(blob, insertedPages) {
  const target = await PDFDocument.load(await blob.arrayBuffer())
  const sources = new Map()

  for (const [pageNumber, { assetId, pageIndex }] of Object.entries(insertedPages)) {
    if (!sources.has(assetId)) {
      const asset = await loadAsset(assetId)
      if (!asset) throw new Error('An inserted PDF file is no longer available.')
      sources.set(assetId, await PDFDocument.load(await asset.arrayBuffer()))
    }

    const targetPage = target.getPage(Number(pageNumber) - 1)
    const [embeddedPage] = await target.embedPdf(sources.get(assetId), [pageIndex])
    const { width, height } = targetPage.getSize()
    const scale = Math.min(width / embeddedPage.width, height / embeddedPage.height)
    targetPage.drawPage(embeddedPage, {
      x: (width - embeddedPage.width * scale) / 2,
      y: (height - embeddedPage.height * scale) / 2,
      xScale: scale,
      yScale: scale,
    })
    moveLastContentStreamToBack(targetPage)
  }

  return new Blob([await target.save()], { type: 'application/pdf' })
}

// Keeps what was rendered on the page, such as the page number, above the inserted page.
function moveLastContentStreamToBack(page) {
  const contents = page.node.Contents()
  if (!(contents instanceof PDFArray) || contents.size() < 2) return
  const last = contents.get(contents.size() - 1)
  contents.remove(contents.size() - 1)
  contents.insert(0, last)
}
