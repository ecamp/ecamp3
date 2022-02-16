// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import lodash from 'lodash'
import Picasso from '../../components/picasso/Picasso.jsx'
import ScheduleEntry from '../../components/scheduleEntry/ScheduleEntry.jsx'
import styles from '../../components/styles.js'
import OpenSans from '../../../../assets/fonts/OpenSans/OpenSans-Regular.ttf'
import OpenSansSemiBold from '../../../../assets/fonts/OpenSans/OpenSans-SemiBold.ttf'
import OpenSansBold from '../../../../assets/fonts/OpenSans/OpenSans-Bold.ttf'

const { Font, Document, Page } = pdf
const { sortBy } = lodash

function PDFDocument (props) {
  const camp = props.store.get(props.config.camp)
  // TODO vary the rendered contents depending on props.config
  return <Document>
    {/* Array.from(Array(50).keys()).map(i => {
      return camp.periods().items.map(period => <Picasso {...props} period={period} key={period.id}/>)
    }) */}
    { camp.periods().items.map(period => <Picasso {...props} period={period} key={period.id}/>) }
    <Page size="A4" orientation="portrait" style={{ ...styles.page, fontSize: 8 + 'pt' }}>
      { camp.periods().items.map(period => {
        return sortBy(period.scheduleEntries().items, ['dayNumber', 'scheduleEntryNumber'])
          .map(scheduleEntry => <ScheduleEntry {...props} scheduleEntry={scheduleEntry} key={scheduleEntry.id}/>)
      }) }
    </Page>
  </Document>
}

const registerFonts = async () => {
  Font.register({
    family: 'OpenSans',
    fonts: [
      // For now it seems that only ttf is supported, not woff or woff2 :(
      { src: OpenSans },
      { src: OpenSansSemiBold, fontWeight: 'semibold' },
      { src: OpenSansBold, fontWeight: 'bold' }
    ]
  })

  return await Promise.all([
    Font.load({ fontFamily: 'OpenSans' }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 600 }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 700 })
  ])
}

PDFDocument.prepare = async (config) => {
  return await registerFonts(config)
}

export default PDFDocument
