// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'

const { View, Text } = pdf

function getWeightsSum (times) {
  return times.reduce((sum, [_, weight]) => sum + weight, 0)
}

function percentage (minutes, times) {
  const hours = minutes / 60.0
  let matchingTimeIndex = times.findIndex(([time, _]) => time >= hours)
  matchingTimeIndex = matchingTimeIndex < 0 ? times.length : matchingTimeIndex
  const weightsSum = getWeightsSum(times.slice(0, matchingTimeIndex))
  const totalWeightsSum = getWeightsSum(times)
  if (totalWeightsSum === 0) {
    return 0
  }
  const result = weightsSum * 100.0 / totalWeightsSum
  return Math.max(0, Math.min(100, result))
}

function filterScheduleEntriesByDay (scheduleEntries, dayNumber, times) {
  // TODO take times into account
  return scheduleEntries.filter(se => (se.periodOffset < (dayNumber * 60 * 24)) && ((se.periodOffset + se.length) > ((dayNumber - 1) * 60 * 24)))
}

const columnStyles = {
  flexGrow: '0',
  display: 'flex',
  flexDirection: 'column'
}
const dayGridStyles = {
  minWidth: '100%',
  minHeight: '100%',
  display: 'flex'
}
const rowStyles = {
  display: 'flex',
  borderBottom: '1px dashed grey'
}
const scheduleEntryStyles = {
  // TODO debug why this absolute positioned view always has full width and 0 height
  position: 'absolute',
  backgroundColor: 'red'
}

function DayColumn ({ times, scheduleEntries, day, styles }) {
  return <View style={{ ...columnStyles, ...styles }}>
    {filterScheduleEntriesByDay(scheduleEntries, day.number, times).map(scheduleEntry => {
      return <View key={scheduleEntry.id} style={{
        scheduleEntryStyles,
        top: percentage(scheduleEntry.periodOffset - (day.dayOffset * 24 * 60), times) + '%',
        bottom: (100 - percentage(scheduleEntry.periodOffset + scheduleEntry.length - (day.dayOffset * 24 * 60), times)) + '%'
      }} debug={true}>
        <Text>{scheduleEntry.activity().title}</Text>
      </View>
    })}
    <View style={ dayGridStyles }>
      {times.map(([time, weight]) => <View key={time} style={{ ...rowStyles, flexGrow: weight }} />)}
    </View>
  </View>
}

export default DayColumn
