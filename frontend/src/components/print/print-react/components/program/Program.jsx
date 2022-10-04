// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Page, View } from '@react-pdf/renderer'
import styles from '../styles.js'
import sortBy from 'lodash/sortBy.js'
import ScheduleEntry from '../scheduleEntry/ScheduleEntry.jsx'

function Program(props) {
  const periods = props.content.options.periods.map((periodUri) =>
    props.store.get(periodUri)
  )
  if (!periods.some((period) => period.scheduleEntries().items.length > 0)) {
    return null
  }
  return (
    <Page size="A4" orientation="portrait" style={{ ...styles.page, fontSize: '8pt' }}>
      {periods.map((period) => {
        if (period.scheduleEntries().items.length === 0) {
          return <React.Fragment key={`${props.id}-${period.id}`} />
        }
        return (
          <View
            key={`${props.id}-${period.id}`}
            id={`${props.id}-${period.id}`}
            bookmark={{ title: period.description, fit: true }}
          >
            {sortBy(period.scheduleEntries().items, [
              'dayNumber',
              'scheduleEntryNumber',
            ]).map((scheduleEntry) => (
              <ScheduleEntry
                {...props}
                scheduleEntry={scheduleEntry}
                key={scheduleEntry.id}
                id={`${props.id}-${period.id}-${scheduleEntry.id}`}
              />
            ))}
          </View>
        )
      })}
    </Page>
  )
}

export default Program
