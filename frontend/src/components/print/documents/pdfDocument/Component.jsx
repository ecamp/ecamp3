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
    { props.config.contents.map(content => {
      if (content.type === 'Picasso') {
        return content.options.periods.map(periodUri => {
          const period = props.store.get(periodUri)
          return <Picasso {...props} period={period} orientation={content.options.orientation} key={period.id} />
        })
      }
      if (content.type === 'Program') {
        return <Page size="A4" orientation="portrait" style={{ ...styles.page, fontSize: 8 + 'pt' }}>
        {
          content.options.periods.map(periodUri => {
            const period = props.store.get(periodUri)
            return sortBy(period.scheduleEntries().items, ['dayNumber', 'scheduleEntryNumber'])
              .map(scheduleEntry => <ScheduleEntry {...props} scheduleEntry={scheduleEntry} key={scheduleEntry.id}/>)
          })
        }
        </Page>
      }
      return <React.Fragment />
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

PDFDocument.filename = async ({ config, $tc }) => {
  if (config.showPicasso && !config.showActivities /* && !config.showSomeOtherStuff */) {
    return $tc('components.camp.print.documents.pdfDocument.filename.picassoOnly', { camp: config.camp.name })
  }
  if (config.showActivities && !config.showPicasso /* && !config.showSomeOtherStuff */) {
    return $tc('components.camp.print.documents.pdfDocument.filename.activitiesOnly', { camp: config.camp.name })
  }
  return config.camp.name
}

export default PDFDocument
