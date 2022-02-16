import * as Comlink from 'comlink'
import { renderPdf } from './renderPdf.js'
import renderingDependencies from './renderingDependencies.js'

const renderPdfInWorker = async (data) => {
  return { ...(await renderPdf(data, renderingDependencies)) }
}

Comlink.expose({
  renderPdfInWorker
})
