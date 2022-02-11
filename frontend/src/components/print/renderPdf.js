// eslint-disable-next-line no-unused-vars
import React from 'react'
// import VueI18n from 'vue-i18n'
import wrap from './minimalHalJsonVuex.js'
import { get } from 'lodash'
import SimplePDF from './SimplePDF.jsx'
import reactPdf from '@react-pdf/renderer'
const { pdf } = reactPdf

export const renderPdf = async ({ config, storeData, translationData }) => {
  const component = printComponentFor(config)

  const result = {
    filename: null, // TODO the component should be able to specify the filename
    blob: null,
    error: null
  }

  // TODO provide proper accessor function for translationData, which supports placeholders and
  //  works independently of whether we are in a web worker or in the main thread.
  const $tc = key => {
    console.log(key, translationData[storeData.lang.language])
    return get(translationData[storeData.lang.language], key, `untranslated key "${key}"`)
  }
  // const i18n = new VueI18n({
  //   messages: JSON.parse(JSON.stringify(translationData))
  // })
  // console.log(i18n)

  const store = wrap(storeData.api)

  if (typeof component.prepare === 'function') {
    await component.prepare(config)
  }
  const document = React.createElement(component, { config, store, $tc })
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
  return SimplePDF
}
