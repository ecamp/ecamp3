declare module 'pdfjs-dist/legacy/build/pdf.min.mjs' {
  import { PDFDocumentProxy } from 'pdfjs-dist'

  export interface PDFDocumentLoadingTask {
    promise: Promise<PDFDocumentProxy>
  }

  export function getDocument(data: { data: Uint8Array }): PDFDocumentLoadingTask
}
