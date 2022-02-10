import { printComponentFor, renderPdf } from './renderPdf.js'
import Worker from 'worker-iife:./renderPdf.worker.js'
import * as Comlink from 'comlink'

export const generatePdf = async (data) => {
  const component = printComponentFor(data.config)
  if (typeof component.prepareInMainThread === 'function') {
    await component.prepareInMainThread(data.config)
  }

  if (data.renderInWorker) {
    const pdfWorker = Comlink.wrap(new Worker())
    const serializableData = {
      ...data,
      config: JSON.parse(JSON.stringify(data.config)),
      storeData: JSON.parse(JSON.stringify(data.storeData)),
      translationData: JSON.parse(JSON.stringify(data.translationData))
    }
    return { ...(await pdfWorker.renderPdfInWorker(serializableData)), filename: 'web-worker.pdf' }
  } else {
    return { ...(await renderPdf(data)), filename: 'main-thread.pdf' }
  }
}
