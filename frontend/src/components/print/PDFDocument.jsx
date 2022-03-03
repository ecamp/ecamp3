// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import lodash from 'lodash'
import Picasso from './picasso/Picasso.jsx'
import ScheduleEntry from './scheduleEntry/ScheduleEntry.jsx'
import styles from './styles.js'
import OpenSans from '../../assets/fonts/OpenSans/OpenSans-Regular.ttf'
import OpenSansSemiBold from '../../assets/fonts/OpenSans/OpenSans-SemiBold.ttf'
import OpenSansBold from '../../assets/fonts/OpenSans/OpenSans-Bold.ttf'

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

export const loadData = async (config) => {
  // Load any data necessary based on the print config
  return Promise.all([
    config.camp()._meta.load,
    config.camp().categories().$loadItems(),
    config.camp().activities().$loadItems().then(activities => {
      return Promise.all(activities.items.map(activity => {
        return Promise.all([
          activity.activityResponsibles().$loadItems(),
          activity.contentNodes().$loadItems()
        ])
      }))
    }),
    config.camp().campCollaborations().$loadItems().then(campCollaboration => {
      return campCollaboration.user ? campCollaboration.user()._meta.load : Promise.resolve()
    }),
    config.camp().periods().$loadItems().then(periods => {
      return Promise.all(periods.items.map(period => {
        return period.scheduleEntries().$loadItems()
      }))
    }),
    config.camp().materialLists().$loadItems(),
    config.apiGet().contentTypes().$loadItems()
  ])
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

PDFDocument.prepareInMainThread = async (config) => {
  return await loadData(config)
}

PDFDocument.prepare = async (config) => {
  return await registerFonts(config)
}

export default PDFDocument
