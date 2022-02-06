import './globalWorkerShim.js'
// eslint-disable-next-line no-unused-vars
import React from 'react'
import * as Comlink from 'comlink'
import OpenSans from '../../assets/fonts/OpenSans/OpenSans-Regular.ttf'
import OpenSansSemiBold from '../../assets/fonts/OpenSans/OpenSans-SemiBold.ttf'
import OpenSansBold from '../../assets/fonts/OpenSans/OpenSans-Bold.ttf'
import SimplePDF from './SimplePDF.jsx'
import reactPdf from '@react-pdf/renderer'
// import VueI18n from 'vue-i18n'
const { pdf, Font } = reactPdf

const generatePdf = async (/* i18nData */) => {
  // const i18n = new VueI18n({
  //   messages: i18nData
  // })
  // console.log(i18n)

  Font.register({
    family: 'OpenSans',
    fonts: [
      // For now it seems that only ttf is supported, not woff or woff2 :(
      { src: OpenSans },
      { src: OpenSansSemiBold, fontWeight: 'semibold' },
      { src: OpenSansBold, fontWeight: 'bold' }
    ]
  })

  await Promise.all([
    Font.load({ fontFamily: 'OpenSans' }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 600 }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 700 })
  ])

  // component should be a react-pdf/renderer document: <Document><Page>...</Page></Document>
  // const document = React.createElement(Document, props)
  const fileName = 'web-worker.pdf'

  const component = React.createElement(SimplePDF, null)
  const pdfBuilder = pdf(component)
  const pdfBlob = await pdfBuilder.toBlob()
  return [fileName, pdfBlob]
}

Comlink.expose({
  generatePdf
})
