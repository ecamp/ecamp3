import { getDocument } from 'pdfjs-dist/legacy/build/pdf.min.mjs'

async function getPdfProperties(buffer: Buffer) {
  const data = new Uint8Array(buffer)
  const loadDocument = getDocument({ data: data })
  const pdfDocument = await loadDocument.promise
  return {
    numPages: pdfDocument.numPages,
  }
}

export { getPdfProperties }
