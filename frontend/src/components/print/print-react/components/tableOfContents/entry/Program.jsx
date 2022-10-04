// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Link, Text } from '@react-pdf/renderer'
import tocStyles from '../tocStyles.js'
import sortBy from 'lodash/sortBy.js'

function Program(props) {
  const periods = props.entry.options.periods.map((periodUri) =>
    props.store.get(periodUri)
  )
  if (!periods.some((period) => period.scheduleEntries().items.length > 0)) {
    return <React.Fragment />
  }
  return periods.map((period) => {
    const periodEntry = (
      <Link style={tocStyles.entry} href={`#${props.id}-${period.id}`}>
        <Text>{period.description}</Text>
      </Link>
    )
    const scheduleEntryEntries = sortBy(period.scheduleEntries().items, [
      'dayNumber',
      'scheduleEntryNumber',
    ]).map((scheduleEntry) => {
      const activity = scheduleEntry.activity()
      return (
        <Link
          style={tocStyles.subEntry}
          href={`#${props.id}-${period.id}-${scheduleEntry.id}`}
          key={scheduleEntry.id}
        >
          <Text>
            {activity.category().short +
              ' ' +
              scheduleEntry.number +
              ' ' +
              activity.title}
          </Text>
        </Link>
      )
    })
    return [periodEntry, ...scheduleEntryEntries]
  })
}

export default Program
