// eslint-disable-next-line no-unused-vars
import React, { useState } from 'react'
import PropTypes from 'prop-types'
import pdf from '@react-pdf/renderer'
import PDFDocument from './PDFDocument.jsx'
import InterRegular from '../../assets/fonts/Inter-Regular.ttf'
import InterSemiBold from '../../assets/fonts/Inter-SemiBold.ttf'

const { Font, PDFDownloadLink } = pdf

if (import.meta.hot) {
  import.meta.hot.accept((newExports) => {
    window.dispatchEvent(new CustomEvent('hotReloadPrintDownload', { detail: newExports.default }))
  })
}

Font.register({
  family: 'Inter',
  fonts: [
    // For now it seems that only ttf is supported, not woff or woff2 :(
    { src: InterRegular },
    { src: InterSemiBold, fontWeight: 'semibold' }
  ]
})

let fontsLoaded = false
let forceUpdate = null

Promise.all([
  Font.load({ fontFamily: 'Inter' }),
  Font.load({ fontFamily: 'Inter', fontWeight: 600 })
]).then(() => {
  fontsLoaded = true
  if (forceUpdate) forceUpdate()
})

function useForceUpdate () {
  const [, setValue] = useState(0)
  return () => setValue(value => value + 1)
}

function PrintDownload (props) {
  forceUpdate = useForceUpdate()
  if (!props.camp || !fontsLoaded) {
    return <div/>
  }
  return <div>
    <PDFDownloadLink document={<PDFDocument {...props} $tc={props.tc} />} fileName={props.camp.name + '.pdf'} className="ml-5 v-btn v-btn--outlined theme--light v-size--default primary--text">
      {({ blob, url, loading, error }) =>
        'Print'
      }
    </PDFDownloadLink>
  </div>
}

PrintDownload.propTypes = {
  camp: PropTypes.object,
  tc: PropTypes.func
}

export default PrintDownload
