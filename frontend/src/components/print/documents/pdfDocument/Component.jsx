// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Font, Document, Page } from '../../reactPdf.js'
import sortBy from 'lodash/sortBy.js'
import Picasso from '../../components/picasso/Picasso.jsx'
import ScheduleEntry from '../../components/scheduleEntry/ScheduleEntry.jsx'
import styles from '../../components/styles.js'
import OpenSans from '../../../../assets/fonts/OpenSans/OpenSans-Regular.ttf'
import OpenSansSemiBold from '../../../../assets/fonts/OpenSans/OpenSans-SemiBold.ttf'
import OpenSansBold from '../../../../assets/fonts/OpenSans/OpenSans-Bold.ttf'

function PDFDocument (props) {
  return <Document>
    { props.config.contents.map((content, idx) => {
      if (content.type === 'Picasso') {
        return content.options.periods.map(periodUri => {
          const period = props.store.get(periodUri)
          return <Picasso {...props} period={period} orientation={content.options.orientation} key={period.id} />
        })
      }
      if (content.type === 'Program') {
        const periods = content.options.periods.map(periodUri => props.store.get(periodUri))
        if (periods.some(period => period.scheduleEntries().items.length > 0)) {
          return <Page size="A4" orientation="portrait" style={{ ...styles.page, fontSize: 8 + 'pt' }} key={idx}>
          {
            periods.map(period => {
              return sortBy(period.scheduleEntries().items, ['dayNumber', 'scheduleEntryNumber'])
                .map(scheduleEntry => <ScheduleEntry {...props} scheduleEntry={scheduleEntry} key={scheduleEntry.id}/>)
            })
          }
          </Page>
        }
      }
      if (content.type === 'Activity' && content.options.scheduleEntry !== null) {
        return <Page size="A4" orientation="portrait" style={{ ...styles.page, fontSize: 8 + 'pt' }} key={idx}>
        {
          [content.options.scheduleEntry].map(scheduleEntryUri => {
            const scheduleEntry = props.store.get(scheduleEntryUri)
            return <ScheduleEntry {...props} scheduleEntry={scheduleEntry} key={scheduleEntry.id} />
          })
        }
        </Page>
      }
      return <React.Fragment key={idx}/>
    })}
  </Document>
}

const registerFonts = async () => {
  Font.register({
    family: 'OpenSans',
    fonts: [
      // For now it seems that only ttf is supported, not woff or woff2
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
