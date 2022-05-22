import { saveAs } from 'file-saver'
import slugify from 'slugify'

const RENDER_IN_WORKER = true

export async function download (options) {
  return new Promise((resolve, reject) => {
    import('./generatePdf.js').then(({ generatePdf }) => {
      generatePdf({
        ...options,
        renderInWorker: RENDER_IN_WORKER
      }).then(({ blob, error }) => {
        if (error) {
          reject(error)
        }

        saveAs(blob, slugify(options.config.documentName, { locale: options.storeData.lang.language.substr(0, 2) }))
        resolve()
      }).catch(error => reject(error))
    }).catch(error => reject(error))
  })
}
