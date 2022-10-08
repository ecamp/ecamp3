// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Link, Text } from '@react-pdf/renderer'
import tocStyles from '../tocStyles.js'

function Activity(props) {
  if (!props.entry.options.scheduleEntry) {
    return <React.Fragment />
  }

  return [props.entry.options.scheduleEntry].map((scheduleEntryUri) => {
    const scheduleEntry = props.store.get(scheduleEntryUri)
    const activity = scheduleEntry.activity()
    return (
      <Link
        style={tocStyles.entry}
        href={`#${props.id}-${scheduleEntry.id}`}
        key={scheduleEntry.id}
      >
        <Text>
          {activity.category().short + ' ' + scheduleEntry.number + ' ' + activity.title}
        </Text>
      </Link>
    )
  })
}

export default Activity
