// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import FrontPage from './FrontPage.jsx'
import Picasso from './picasso/Picasso.jsx'
import ScheduleEntry from './scheduleEntry/ScheduleEntry.jsx'

const { Document } = pdf

function PDFDocument (props) {
  return <Document>
    {/* Array.from(Array(50).keys()).map(i => {
      return props.camp.periods().items.map(period => <Picasso {...props} period={period} key={period.id}/>)
    }) */}
    { props.camp.periods().items.map(period => <Picasso {...props} period={period} key={period.id}/>) }
    { props.camp.periods().items.map(period => {
      return period.scheduleEntries().items.map(scheduleEntry => <ScheduleEntry {...props} scheduleEntry={scheduleEntry} key={scheduleEntry.id}/>)
    }) }
    {/* <FrontPage {...props} /> */}
  </Document>
}

export default PDFDocument
