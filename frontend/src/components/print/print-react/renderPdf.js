export const renderPdf = async (
  { config, storeData, translationData },
  { React, wrap, createI18n, pdf, documents }
) => {
  const result = {
    filename: null,
    blob: null,
    error: null,
  }

  try {
    const document = documents[await documentFor(config)]

    const { translate } = createI18n(translationData, storeData.lang.language)
    const store = wrap(storeData.api)

    if (typeof document.prepare === 'function') {
      await document.prepare(config)
    }

    config.camp = store.get(config.camp)
    const props = { config, store, $tc: translate }

    result.filename = config.documentName
    result.blob = await pdf(React.createElement(document, props)).toBlob()
  } catch (error) {
    result.error = error
  }

  return result
}

const documentFor = () => {
  // If necessary, this could select a different main document component, depending on the print config
  return 'pdfDocument'
}

export const mainThreadLoaderFor = async (config) => {
  const document = documentFor(config)
  return (await import('./documents/' + document + '/prepareInMainThread.js')).default
}
