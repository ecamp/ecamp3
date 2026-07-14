import FontStore from '@react-pdf/font'
import layoutDocument from '@react-pdf/layout'
import PDFDocument from '@react-pdf/pdfkit'
import renderPDF from '@react-pdf/render'

const fontStore = new FontStore()

export async function renderPdfStructureToReactPdf(
  pdfStructure,
  compress = true,
  onProgress = async () => {}
) {
  await onProgress('layoutDocument')

  let page = 0
  globalThis.addEventListener('layoutPage', async () => {
    page++
    await onProgress('layoutPage', { page })
  })

  const layout = await layoutDocument(pdfStructure, fontStore)

  const totalPages = layout.children.length
  page = 0
  await onProgress('renderingPdfPage', { page, totalPages })

  const documentProps = pdfStructure.props || {}
  const { pdfVersion, language, pageLayout, pageMode } = documentProps

  const ctx = new PDFDocument({
    compress,
    pdfVersion,
    lang: language,
    displayTitle: true,
    autoFirstPage: false,
    pageLayout,
    pageMode,
  })

  ctx.on('pageAdded', async () => {
    page++
    await onProgress('renderingPdfPage', { page, totalPages })
  })

  return renderPDF(ctx, layout)
}

const Font = fontStore

export { Font }
