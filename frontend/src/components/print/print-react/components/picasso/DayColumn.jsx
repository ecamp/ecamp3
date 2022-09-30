// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View } from '../../reactPdf.js'
import ScheduleEntry from './ScheduleEntry.jsx'
import dayjs from '@/common/helpers/dayjs.js'

// converts ISO String format (UTC timezone) into a unix/seconds timestamp (UTC timezone)
function stringToTimestamp(string) {
  return dayjs.utc(string).unix()
}

function getWeightsSum(times) {
  return times.reduce((sum, [_, weight]) => sum + weight, 0)
}

function percentage(seconds, times) {
  const hours = seconds / 3600.0
  let matchingTimeIndex = times.findIndex(([time, _]) => time >= hours)
  matchingTimeIndex = Math.min(
    matchingTimeIndex < 0 ? times.length : matchingTimeIndex,
    times.length - 1
  )
  const remainder = hours - times[matchingTimeIndex][0]
  const weightsSum =
    getWeightsSum(times.slice(0, matchingTimeIndex)) +
    remainder * times[Math.min(matchingTimeIndex, times.length)][1]
  const totalWeightsSum = getWeightsSum(times)
  if (totalWeightsSum === 0) {
    return 0
  }
  const result = (weightsSum * 100.0) / totalWeightsSum
  return Math.max(0, Math.min(100, result))
}

function dayBoundariesInMinutes(day, times) {
  const [dayStart] = times[0]
  const [dayEnd] = times[times.length - 1]

  const dayStartTimestamp = stringToTimestamp(day.start)

  const dayStartMinutes = dayStartTimestamp + dayStart * 3600
  const dayEndMinutes = dayStartTimestamp + dayEnd * 3600

  return [dayStartMinutes, dayEndMinutes]
}

function filterScheduleEntriesByDay(scheduleEntries, day, times) {
  const [dayStart, dayEnd] = dayBoundariesInMinutes(day, times)

  return scheduleEntries.filter((scheduleEntry) => {
    return (
      stringToTimestamp(scheduleEntry.start) < dayEnd &&
      stringToTimestamp(scheduleEntry.end) > dayStart
    )
  })
}

function scheduleEntryBorderRadiusStyles(scheduleEntry, day, times) {
  const [dayStart, dayEnd] = dayBoundariesInMinutes(day, times)

  const start = stringToTimestamp(scheduleEntry.start)
  const startsOnThisDay = start >= dayStart && start <= dayEnd

  const end = stringToTimestamp(scheduleEntry.end)
  const endsOnThisDay = end >= dayStart && end <= dayEnd

  return {
    // prettier-ignore
    ...(endsOnThisDay ? {} : { borderBottomRightRadius: '0', borderBottomLeftRadius: '0' }),
    ...(startsOnThisDay ? {} : { borderTopRightRadius: '0', borderTopLeftRadius: '0' }),
  }
}

function scheduleEntryPositionStyles(scheduleEntry, day, times) {
  return {
    top:
      percentage(
        stringToTimestamp(scheduleEntry.start) - stringToTimestamp(day.start),
        times
      ) + '%',
    bottom:
      100 -
      percentage(
        stringToTimestamp(scheduleEntry.end) - stringToTimestamp(day.start),
        times
      ) +
      '%',
  }
}

const columnStyles = {
  flexBasis: 0,
  flexGrow: 1,
  display: 'flex',
  flexDirection: 'column',
  overflow: 'hidden',
  position: 'relative',
}
const dayGridStyles = {
  width: '100%',
  height: '100%',
  display: 'flex',
  flexDirection: 'column',
}
const dayGridRowStyles = {
  display: 'flex',
  flexBasis: 0,
}
const scheduleEntryColumnStyles = {
  margin: '0 2pt',
  position: 'absolute',
  top: '0',
  bottom: '0',
  left: '0',
  right: '0',
}

function DayColumn({ times, scheduleEntries, day, styles }) {
  return (
    <View style={{ ...columnStyles, ...styles }}>
      <View style={dayGridStyles}>
        {times.slice(0, times.length - 1).map(([time, weight], index) => (
          <View
            key={time}
            style={{
              ...dayGridRowStyles,
              flexGrow: weight,
              ...(index % 2 === 0 ? { backgroundColor: 'lightgrey' } : {}),
            }}
          />
        ))}
      </View>
      <View style={scheduleEntryColumnStyles}>
        {filterScheduleEntriesByDay(scheduleEntries, day, times).map((scheduleEntry) => {
          return (
            <ScheduleEntry
              key={scheduleEntry.id}
              scheduleEntry={scheduleEntry}
              styles={{
                ...scheduleEntryPositionStyles(scheduleEntry, day, times),
                ...scheduleEntryBorderRadiusStyles(scheduleEntry, day, times),
              }}
            />
          )
        })}
      </View>
    </View>
  )
}

export default DayColumn
