// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import ScheduleEntry from './ScheduleEntry.jsx'

const { View } = pdf

function getWeightsSum (times) {
  return times.reduce((sum, [_, weight]) => sum + weight, 0)
}

function percentage (minutes, times) {
  const hours = minutes / 60.0
  let matchingTimeIndex = times.findIndex(([time, _]) => time >= hours)
  matchingTimeIndex = Math.min(matchingTimeIndex < 0 ? times.length : matchingTimeIndex, times.length - 1)
  const remainder = hours - times[matchingTimeIndex][0]
  const weightsSum = getWeightsSum(times.slice(0, matchingTimeIndex)) + remainder * times[Math.min(matchingTimeIndex, times.length)][1]
  const totalWeightsSum = getWeightsSum(times)
  if (totalWeightsSum === 0) {
    return 0
  }
  const result = weightsSum * 100.0 / totalWeightsSum
  return Math.max(0, Math.min(100, result))
}

function dayBoundariesInMinutes (day, times) {
  const [dayStart] = times[0]
  const [dayEnd] = times[times.length - 1]
  const dayStartMinutes = (day.dayOffset * 24 + dayStart) * 60
  const dayEndMinutes = (day.dayOffset * 24 + dayEnd) * 60

  return [dayStartMinutes, dayEndMinutes]
}

function filterScheduleEntriesByDay (scheduleEntries, day, times) {
  const [dayStart, dayEnd] = dayBoundariesInMinutes(day, times)

  return scheduleEntries.filter(scheduleEntry => {
    return (scheduleEntry.periodOffset < dayEnd) &&
      ((scheduleEntry.periodOffset + scheduleEntry.length) > dayStart)
  })
}

function scheduleEntryBorderRadiusStyles (scheduleEntry, day, times) {
  const [dayStart, dayEnd] = dayBoundariesInMinutes(day, times)

  const start = scheduleEntry.periodOffset
  const startsOnThisDay = start >= dayStart && start <= dayEnd

  const end = scheduleEntry.periodOffset + scheduleEntry.length
  const endsOnThisDay = end >= dayStart && end <= dayEnd

  return {
    ...(endsOnThisDay ? {} : { borderBottomRightRadius: '0', borderBottomLeftRadius: '0' }),
    ...(startsOnThisDay ? {} : { borderTopRightRadius: '0', borderTopLeftRadius: '0' })
  }
}

function scheduleEntryPositionStyles (scheduleEntry, day, times) {
  return {
    top: percentage(scheduleEntry.periodOffset - (day.dayOffset * 24 * 60), times) + '%',
    bottom: (100 - percentage(scheduleEntry.periodOffset + scheduleEntry.length - (day.dayOffset * 24 * 60), times)) + '%'
  }
}

const columnStyles = {
  flexGrow: '1',
  display: 'flex',
  flexDirection: 'column',
  overflow: 'hidden'
}
const dayGridStyles = {
  minWidth: '100%',
  minHeight: '100%',
  display: 'flex'
}
const rowStyles = {
  display: 'flex'
}
const scheduleEntryColumnStyles = {
  margin: '0 0.5%',
  position: 'absolute',
  top: '0',
  bottom: '0',
  left: '0',
  right: '0'
}

function DayColumn ({ times, scheduleEntries, day, styles }) {
  return <View style={{ ...columnStyles, ...styles }}>
    <View style={ dayGridStyles }>
      {times.slice(0, times.length - 1).map(([time, weight], index) => <View key={time} style={{
        ...rowStyles,
        flexGrow: weight,
        ...(index % 2 === 0 ? { backgroundColor: 'lightgrey' } : {})
      }} />)}
    </View>
    <View style={ scheduleEntryColumnStyles }>
      {filterScheduleEntriesByDay(scheduleEntries, day, times).map(scheduleEntry => {
        return <ScheduleEntry key={scheduleEntry.id} scheduleEntry={scheduleEntry} styles={{
          ...scheduleEntryPositionStyles(scheduleEntry, day, times),
          ...scheduleEntryBorderRadiusStyles(scheduleEntry, day, times)
        }}/>
      })}
    </View>
  </View>
}

export default DayColumn
