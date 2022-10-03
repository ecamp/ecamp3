// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '@react-pdf/renderer'
import Responsibles from './Responsibles.jsx'
import picassoStyles from './picassoStyles.js'

function scheduleEntryTitle(scheduleEntry) {
  return (
    scheduleEntry.activity().category().short +
    ' ' +
    scheduleEntry.number +
    ' ' +
    scheduleEntry.activity().title
  )
}

function ScheduleEntry({ scheduleEntry, styles }) {
  return (
    <View
      style={{
        ...picassoStyles.scheduleEntry,
        left: 0,
        right: 0,
        backgroundColor: scheduleEntry.activity().category().color,
        ...styles,
      }}
    >
      <View style={picassoStyles.scheduleEntrySpacer} />
      <Text style={picassoStyles.scheduleEntryTitle}>
        {scheduleEntryTitle(scheduleEntry)}
      </Text>
      <View style={picassoStyles.scheduleEntrySpacer} />
      <View style={picassoStyles.scheduleEntryResponsiblesContainer}>
        <View style={picassoStyles.scheduleEntrySpacer} />
        <Responsibles
          styles={picassoStyles.scheduleEntryResponsible}
          activity={scheduleEntry.activity()}
        />
        <View style={picassoStyles.scheduleEntrySpacer} />
      </View>
    </View>
  )
}

export default ScheduleEntry
