import * as Sentry from '@sentry/browser'
import * as Comlink from 'comlink'
import { renderPdf } from './renderPdf.js'
import './globalWorkerShim.js'
import 'raf/polyfill' // must be imported before renderingDependencies
import renderingDependencies from './renderingDependencies.js'

if (typeof importScripts === 'function') {
  self.importScripts('/environment.js')
}

const renderPdfInWorker = async (data) => {
  const result = { ...(await renderPdf(data, renderingDependencies)) }
  if (result.error) {
    Sentry.captureException(result.error)
    result.error = result.error.message
  }
  return result
}

Comlink.expose({
  renderPdfInWorker
})
