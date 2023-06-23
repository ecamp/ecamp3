import wrap from '../../minimalHalJsonVuex.js'
import createI18n from '../../i18n.js'
import { render, prepare } from '@/pdf/pdf.mjs'

export const renderPdf = async ({ config, storeData, translationData }) => {
  const result = {
    filename: null,
    blob: null,
    error: null,
  }

  try {
    const { translate } = createI18n(translationData, storeData.lang.language)
    const store = wrap(storeData.api)

    if (typeof prepare === 'function') {
      await prepare(config)
    }

    config.camp = store.get(config.camp)
    const props = { config, store, $tc: translate, locale: storeData.lang.language }

    result.filename = config.documentName
    result.blob = await render(props).toBlob()
  } catch (error) {
    result.error = error
  }

  return result
}
