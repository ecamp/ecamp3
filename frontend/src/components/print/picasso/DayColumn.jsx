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

function filterScheduleEntriesByDay (scheduleEntries, dayOffset, times) {
  const [dayStart] = times[0]
  const [dayEnd] = times[times.length - 1]
  const dayStartMinutes = (dayOffset * 24 + dayStart) * 60
  const dayEndMinutes = (dayOffset * 24 + dayEnd) * 60
  return scheduleEntries.filter(se => {
    return (se.periodOffset < dayEndMinutes) &&
      ((se.periodOffset + se.length) > dayStartMinutes)
  })
}

const columnStyles = {
  flexGrow: '1',
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
    {filterScheduleEntriesByDay(scheduleEntries, day.dayOffset, times).map(scheduleEntry => {
      return <View key={scheduleEntry.id} style={{
        scheduleEntryStyles,
        top: percentage(scheduleEntry.periodOffset - (day.dayOffset * 24 * 60), times) + '%',
        bottom: (100 - percentage(scheduleEntry.periodOffset + scheduleEntry.length - (day.dayOffset * 24 * 60), times)) + '%'
      }} debug={true}>
        <Text>{scheduleEntry.activity().title}</Text>
      </View>
    })}
    <View style={ dayGridStyles }>
      {times.slice(0, times.length - 1).map(([time, weight]) => <View key={time} style={{ ...rowStyles, flexGrow: weight }} />)}
    </View>
  </View>
}

export default DayColumn
