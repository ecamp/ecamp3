export const renderPdf = async ({ config, storeData, translationData }, { React, wrap, createI18n, pdf, documents }) => {
  const document = documents[await documentFor(config)]

  const result = {
    filename: null, // TODO the document component itself should be able to specify the filename
    blob: null,
    error: null
  }

  const { translate } = createI18n(translationData, storeData.lang.language)
  const store = wrap(storeData.api)

  if (typeof document.prepare === 'function') {
    await document.prepare(config)
  }
  const reactComponent = React.createElement(document, { config, store, $tc: translate })
  const pdfBuilder = pdf(reactComponent)
  try {
    result.blob = await pdfBuilder.toBlob()
  } catch (error) {
    result.error = error
  }

  return result
}

const documentFor = (config) => {
  // If necessary, this could select a different main document component, depending on the print config
  return 'pdfDocument'
}

export const mainThreadLoaderFor = async (config) => {
  const document = documentFor(config)
  return (await import(`./documents/${document}/prepareInMainThread.js`)).default
}
