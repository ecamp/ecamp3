// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '@react-pdf/renderer'
import Responsibles from './Responsibles.jsx'

const fontSize = 8

function scheduleEntryTitle(scheduleEntry) {
  return (
    scheduleEntry.activity().category().short +
    ' ' +
    scheduleEntry.number +
    ' ' +
    scheduleEntry.activity().title
  )
}

const scheduleEntryStyles = {
  position: 'absolute',
  borderRadius: '2pt',
  padding: '0 4pt',
  flexDirection: 'column',
  justifyContent: 'flex-start',
}

const titleStyles = {
  fontSize: fontSize + 'pt',
  flexShrink: '0',
}

const responsiblesContainerStyle = {
  position: 'absolute',
  top: '0',
  bottom: '0',
  right: '0',
  left: '0',
  flexDirection: 'column',
  alignItems: 'flex-end',
  justifyContent: 'flex-end',
  padding: '0 4pt',
}

const responsiblesStyle = {
  flexShrink: '0',
}

const spacerStyles = {
  flexBasis: '4pt',
  flexShrink: '1',
}

function ScheduleEntry({ scheduleEntry, styles }) {
  return (
    <View
      style={{
        left: scheduleEntry.left * 100 + '%',
        right: (1.0 - scheduleEntry.left - scheduleEntry.width) * 100 + '%',
        backgroundColor: scheduleEntry.activity().category().color,
        ...scheduleEntryStyles,
        ...styles,
      }}
    >
      <View style={spacerStyles} />
      <Text style={titleStyles}>{scheduleEntryTitle(scheduleEntry)}</Text>
      <View style={spacerStyles} />
      <View style={responsiblesContainerStyle}>
        <View style={spacerStyles} />
        <Responsibles styles={responsiblesStyle} activity={scheduleEntry.activity()} />
        <View style={spacerStyles} />
      </View>
    </View>
  )
}

export default ScheduleEntry
