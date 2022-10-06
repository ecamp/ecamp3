import * as Sentry from '@sentry/browser'
import { renderPdf } from './renderPdf.js'
import './globalWorkerShim.js'
import 'raf/polyfill' // must be imported before renderingDependencies
import renderingDependencies from './renderingDependencies.js'

if (typeof importScripts === 'function') {
  self.importScripts('/environment.js')

  // Work around an incompatibility between comlink, vite and react-pdf...
  // Comlink works with a globally defined function 'start'. And apparently the way we import
  // react-pdf in our worker registers some of its functions in the global scope, including
  // a 'start' function which @react-pdf/textkit uses internally for processing text strings.
  // When comlink finds this global 'start' function, it calls it without arguments.
  // But the function in react-pdf expects some arguments and will produce the error
  // 'TypeError: attributedString is undefined'
  // So if we detect a call without arguments, we simply ignore that call.
  const originalStart = self.start
  self.start = (...args) => {
    if (args.length === 0) return
    return originalStart(...args)
  }
}

export const renderPdfInWorker = async (data) => {
  const result = { ...(await renderPdf(data, renderingDependencies)) }
  if (result.error) {
    Sentry.captureException(result.error)
    result.error = result.error.message
  }
  return result
}
