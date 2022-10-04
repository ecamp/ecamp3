// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Page } from '@react-pdf/renderer'
import styles from '../styles.js'
import ScheduleEntry from '../scheduleEntry/ScheduleEntry.jsx'

function Activity(props) {
  if (!props.content.options.scheduleEntry) {
    return <React.Fragment />
  }
  return (
    <Page size="A4" orientation="portrait" style={{ ...styles.page, fontSize: '8pt' }}>
      {[props.content.options.scheduleEntry].map((scheduleEntryUri) => {
        const scheduleEntry = props.store.get(scheduleEntryUri)
        return (
          <ScheduleEntry
            {...props}
            scheduleEntry={scheduleEntry}
            key={scheduleEntry.id}
            id={`${props.id}-${scheduleEntry.id}`}
          />
        )
      })}
    </Page>
  )
}

export default Activity
