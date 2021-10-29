// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'

const { View, Text } = pdf

const scheduleEntryStyles = {
  position: 'absolute',
  backgroundColor: 'blue',
  opacity: '0.5'
}

function DayColumn ({ scheduleEntry, styles }) {
  return <View key={scheduleEntry.id} style={{
    left: scheduleEntry.left * 100 + '%',
    right: (1.0 - scheduleEntry.left - scheduleEntry.width) * 100 + '%',
    ...scheduleEntryStyles,
    ...styles
  }}>
    <Text>{scheduleEntry.activity().title}</Text>
  </View>
}

export default DayColumn
