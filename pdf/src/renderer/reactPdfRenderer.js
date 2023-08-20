import FontStore from '@react-pdf/font'
import layoutDocument from '@react-pdf/layout'
import PDFDocument from '@react-pdf/pdfkit'
import renderPDF from '@react-pdf/render'

const fontStore = new FontStore()

export async function renderPdfStructureToReactPdf(pdfStructure, compress = true) {
  const layout = await layoutDocument(pdfStructure, fontStore)

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

  return renderPDF(ctx, layout)
}

const Font = fontStore

export { Font }
