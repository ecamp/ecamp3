import * as Comlink from 'comlink'
import { renderPdf } from './renderPdf.js'

const renderPdfInWorker = async (data) => {
  return { ...(await renderPdf(data)), filename: 'web-worker.pdf' }
}

Comlink.expose({
  renderPdfInWorker
})
