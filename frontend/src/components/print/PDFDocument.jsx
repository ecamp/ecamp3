// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import FrontPage from './FrontPage.jsx'
import Picasso from './picasso/Picasso.jsx'

const { Document } = pdf

function PDFDocument (props) {
  return <Document>
    {props.camp.periods().items.map(period => <Picasso {...props} period={period} key={period.id} />)}
    {/* <FrontPage {...props} /> */}
  </Document>
}

export default PDFDocument
