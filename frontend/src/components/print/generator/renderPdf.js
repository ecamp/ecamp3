import wrap from './minimalHalJsonVuex.js'
import createI18n from './i18n.js'
import { render, prepare } from '@/pdf/index.js'
import { insertPdfPages } from './insertPdfPages.js'

export const renderPdf = async ({ config, storeData, translationData }, onProgress) => {
  const result = {
    filename: null,
    blob: null,
    error: null,
  }

  try {
    await onProgress('setupTranslationsAndFonts')

    const { translate } = createI18n(translationData, storeData.lang.language)
    const store = wrap(storeData.api)

    if (typeof prepare === 'function') {
      await prepare(config)
    }

    config.camp = store.get(config.camp)
    const insertedPages = {}
    const props = {
      config,
      store,
      $tc: translate,
      locale: storeData.lang.language,
      insertedPages,
    }

    result.filename = config.documentName
    const blob = await render(props, onProgress).toBlob()
    result.blob =
      Object.keys(insertedPages).length > 0
        ? await insertPdfPages(blob, insertedPages)
        : blob
  } catch (error) {
    result.error = error
  }

  return result
}
