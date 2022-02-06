// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'

const { Document, Page, Text } = pdf

function SimplePDF (props) {
  return <Document>
    <Page>
      <Text style={{ fontFamily: 'OpenSans' }}>hello world!</Text>
    </Page>
  </Document>
}

export default SimplePDF
