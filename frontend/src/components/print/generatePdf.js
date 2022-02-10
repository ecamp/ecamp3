import { printComponentFor, renderPdf } from './renderPdf.js'
// import Worker from 'worker-iife:./renderPdf.worker.js'
// import * as Comlink from 'comlink'

export const generatePdf = async (data) => {
  const component = printComponentFor(data.config)
  if (typeof component.prepareInMainThread === 'function') {
    await component.prepareInMainThread(data.config)
  }

  if (data.renderInWorker) {
    // TODO
    // const pdfWorker = Comlink.wrap(new Worker())
    // const [filename, blob] = await pdfWorker.renderPdfInWorker({ camp: () => {} }, {}, {})
    // something something error handling
    // console.log(filename, blob)
    // state.blob = blob
    return {}
  } else {
    return { ...(await renderPdf(data)), filename: 'main-thread.pdf' }
  }
}
