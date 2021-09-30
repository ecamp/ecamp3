// eslint-disable-next-line no-unused-vars
import React, { useState } from 'react'
import PropTypes from 'prop-types'
import pdf from '@react-pdf/renderer'
import PDFDocument from '@/components/print/PDFDocument.jsx'
import InterRegular from '../../assets/fonts/Inter-Regular.ttf'
import InterSemiBold from '../../assets/fonts/Inter-SemiBold.ttf'

const { Font, PDFViewer, PDFDownloadLink } = pdf

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

function PrintPreview (props) {
  forceUpdate = useForceUpdate()
  if (!props.camp || !fontsLoaded) {
    return <div/>
  }
  return <div>
    <PDFDownloadLink document={<PDFDocument {...props} />} fileName={props.camp.name + '.pdf'}>
      {({ blob, url, loading, error }) =>
        loading ? 'Loading document...' : 'Download now!'
      }
    </PDFDownloadLink>
    <PDFViewer width="100%" height="500">
      <PDFDocument {...props} />
    </PDFViewer>
  </div>
}

PrintPreview.propTypes = {
  camp: PropTypes.object,
  $tc: PropTypes.func
}

export default PrintPreview
