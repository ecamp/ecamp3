import React from 'react'
import wrap from '../../minimalHalJsonVuex.js'
import createI18n from '../../i18n.js'
import { pdf } from '@react-pdf/renderer'
import documentComponent from './Component.jsx'

export const renderPdf = async ({ config, storeData, translationData }) => {
  const result = {
    filename: null,
    blob: null,
    error: null,
  }

  try {
    const { translate } = createI18n(translationData, storeData.lang.language)
    const store = wrap(storeData.api)

    if (typeof documentComponent.prepare === 'function') {
      await documentComponent.prepare(config)
    }

    config.camp = store.get(config.camp)
    const props = { config, store, $tc: translate }

    result.filename = config.documentName
    result.blob = await pdf(React.createElement(documentComponent, props)).toBlob()
  } catch (error) {
    result.error = error
  }

  return result
}
