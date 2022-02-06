// eslint-disable-next-line no-unused-vars
import React from 'react'
import PropTypes from 'prop-types'
import * as Comlink from 'comlink'
import { useAsyncCallback } from 'react-async-hook'
// import PDFDocument from './PDFDocument.jsx'
import { saveAs } from 'file-saver'
import Worker from 'worker-iife:./renderPdf.worker.js'

if (import.meta.hot) {
  import.meta.hot.accept((newExports) => {
    window.dispatchEvent(new CustomEvent('hotReloadPrintPreview', { detail: newExports.default }))
  })
}

function PDFDownloadButton (props) {
  const exportAction = useAsyncCallback(async () => {
    const pdfWorker = Comlink.wrap(new Worker())

    const [filename, blob] = await pdfWorker.generatePdf(/* props['i18n-data'] */)
    console.log(filename, blob)
    saveAs(blob, filename)
  })
  return <button className="ml-5 v-btn v-btn--outlined theme--light v-size--default primary--text" onClick={exportAction.execute} disabled={exportAction.loading}>
    {exportAction.loading ? 'Generating...' : 'Generate PDF'}
  </button>
}

PDFDownloadButton.propTypes = {
  camp: PropTypes.object,
  i18nData: PropTypes.object
}

export default PDFDownloadButton
