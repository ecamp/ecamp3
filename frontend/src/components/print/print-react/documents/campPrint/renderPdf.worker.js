import * as Sentry from '@sentry/browser'
import '../../globalWorkerShim.js'
import 'raf/polyfill' // must be imported before renderPdf
import { renderPdf } from './renderPdf.js'
import dayjs, { dayjsLocaleMap } from '@/common/helpers/dayjs.js'

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

export const renderPdfInWorker = async (data) => {
  const lang = data?.config?.language
  if (!lang) {
    const error = 'language was undefined in react print config'
    Sentry.captureException(new Error(error))
    return { error }
  }

  // We need to set the locale again here. Otherwise dayjs falls back to the default
  // on production deployments
  dayjs.locale(Object.keys(dayjsLocaleMap).includes(lang) ? dayjsLocaleMap[lang] : lang)

  const result = { ...(await renderPdf(data)) }
  if (result.error) {
    Sentry.captureException(result.error)
    result.error = result.error.message
  }
  return result
}
