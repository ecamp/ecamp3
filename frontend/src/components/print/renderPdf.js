import React from 'react'
import wrap from './minimalHalJsonVuex.js'
import createI18n from './i18n.js'
import reactPdf from '@react-pdf/renderer'
import PDFDocument from './PDFDocument.jsx'
const { pdf } = reactPdf

export const renderPdf = async ({ config, storeData, translationData }) => {
  const component = printComponentFor(config)

  const result = {
    filename: null, // TODO the component should be able to specify the filename
    blob: null,
    error: null
  }

  const { translate } = createI18n(translationData, storeData.lang.language)
  const store = wrap(storeData.api)

  if (typeof component.prepare === 'function') {
    await component.prepare(config)
  }
  const document = React.createElement(component, { config, store, $tc: translate })
  const pdfBuilder = pdf(document)
  try {
    result.blob = await pdfBuilder.toBlob()
  } catch (error) {
    result.error = error
  }

  return result
}

export const printComponentFor = (config) => {
  // TODO select a different component depending on the config
  return PDFDocument
}
