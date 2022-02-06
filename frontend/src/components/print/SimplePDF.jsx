// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'

const { Document, Page, Text } = pdf

function SimplePDF (props) {
  return <Document>
    <Page>
      { Array.from(new Array(1000).keys()).map(i => <Text key={i} style={{ fontFamily: 'OpenSans' }}>Hello world! I was rendered in a background job!</Text>) }
    </Page>
  </Document>
}

export default SimplePDF
