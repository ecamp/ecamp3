// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import Responsibles from './Responsibles.jsx'

const { View, Text } = pdf

const fontSize = 8

function scheduleEntryTitle (scheduleEntry) {
  return scheduleEntry.activity().category().short + ' ' + scheduleEntry.number + ' ' + scheduleEntry.activity().title
}

const scheduleEntryStyles = {
  position: 'absolute',
  borderRadius: '2px'
}

const contentStyles = {
  margin: '3px',
  flexDirection: 'column',
  height: '100%',
  justifyContent: 'flex-start'
}

const titleStyles = {
  fontSize: fontSize + 'pt',
  overflow: 'hidden',
  minHeight: '100%'
}

const responsiblesStyle = {
  position: 'absolute',
  top: '0',
  bottom: '2px',
  right: '4px'
}

function ScheduleEntry ({ scheduleEntry, styles }) {
  return <View style={{
    left: scheduleEntry.left * 100 + '%',
    right: (1.0 - scheduleEntry.left - scheduleEntry.width) * 100 + '%',
    backgroundColor: scheduleEntry.activity().category().color,
    ...scheduleEntryStyles,
    ...styles
  }}>
    <View style={contentStyles}>
      <Text style={titleStyles}>{scheduleEntryTitle(scheduleEntry)}</Text>
    </View>
    <Responsibles styles={responsiblesStyle} scheduleEntry={scheduleEntry} />
  </View>
}

export default ScheduleEntry
