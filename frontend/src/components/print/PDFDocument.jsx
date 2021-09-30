// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import FrontPage from '@/components/print/FrontPage'
import Picasso from '@/components/print/Picasso'

const { Document } = pdf

function PDFDocument (props) {
  return <Document>
    <FrontPage {...props} />
    <Picasso {...props} />
  </Document>
}

export default PDFDocument
