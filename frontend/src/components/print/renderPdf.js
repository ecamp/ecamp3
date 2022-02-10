// eslint-disable-next-line no-unused-vars
import React from 'react'
// import VueI18n from 'vue-i18n'
import reactPdf from '@react-pdf/renderer'
import SimplePDF from './SimplePDF.jsx'
const { pdf } = reactPdf

export const renderPdf = async ({ config, storeData, translationData }) => {
  const component = printComponentFor(config)

  const result = {
    filename: null, // TODO the component should be able to specify the filename
    blob: null,
    error: null
  }

  // TODO provide accessor functions for storeData and translationData, which work independently of
  //   whether we are in a web worker or in the main thread.
  // const i18n = new VueI18n({
  //   messages: JSON.parse(JSON.stringify(translationData))
  // })
  // console.log(i18n)

  if (typeof component.prepare === 'function') {
    await component.prepare(config)
  }
  const document = React.createElement(component, { config })
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
