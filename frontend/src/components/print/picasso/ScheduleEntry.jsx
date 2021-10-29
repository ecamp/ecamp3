// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'

const { View, Text } = pdf

function scheduleEntryTitle (scheduleEntry) {
  return scheduleEntry.activity().category().short + ' ' + scheduleEntry.number + ' ' + scheduleEntry.activity().title
}

const scheduleEntryStyles = {
  position: 'absolute',
  borderRadius: '2px'
}

function DayColumn ({ scheduleEntry, styles }) {
  return <View key={scheduleEntry.id} style={{
    left: scheduleEntry.left * 100 + '%',
    right: (1.0 - scheduleEntry.left - scheduleEntry.width) * 100 + '%',
    backgroundColor: scheduleEntry.activity().category().color,
    ...scheduleEntryStyles,
    ...styles
  }}>
    <View style={{ margin: '5px' }}>
      <Text>{scheduleEntryTitle(scheduleEntry)}</Text>
    </View>
  </View>
}

export default DayColumn
