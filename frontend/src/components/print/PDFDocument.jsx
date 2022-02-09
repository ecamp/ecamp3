// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import { sortBy } from 'lodash'
import Picasso from './picasso/Picasso.jsx'
import ScheduleEntry from './scheduleEntry/ScheduleEntry.jsx'
import styles from './styles.js'

const { Document, Page } = pdf

function PDFDocument (props) {
  return <Document>
    {/* Array.from(Array(50).keys()).map(i => {
      return props.camp.periods().items.map(period => <Picasso {...props} period={period} key={period.id}/>)
    }) */}
    { props.camp.periods().items.map(period => <Picasso {...props} period={period} key={period.id}/>) }
    <Page size="A4" orientation="portrait" style={{ ...styles.page, fontSize: 8 + 'pt' }}>
      { props.camp.periods().items.map(period => {
        return sortBy(period.scheduleEntries().items, ['dayNumber', 'scheduleEntryNumber'])
          .map(scheduleEntry => <ScheduleEntry {...props} scheduleEntry={scheduleEntry} key={scheduleEntry.id}/>)
      }) }
    </Page>
  </Document>
}

export default PDFDocument
