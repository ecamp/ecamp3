// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View } from '@react-pdf/renderer'
import ScheduleEntry from './ScheduleEntry.jsx'
import dayjs from '@/common/helpers/dayjs.js'
import picassoStyles from './picassoStyles.js'
import vuetifyLayouter, * as vuetifyLayouterInMainThread from 'vuetify/es5/components/VCalendar/modes/column.js'
import vuetifyEvents, * as vuetifyEventsInMainThread from 'vuetify/es5/components/VCalendar/util/events.js'
import { utcStringToTimestamp } from '../../../../../../../common/helpers/dateHelperVCalendar.js'
import keyBy from 'lodash/keyBy.js'

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

function scheduleEntryPositionStyles(scheduleEntry, day, times, leftAndWidth) {
  const left = leftAndWidth[scheduleEntry.id]?.left || 0
  const width = leftAndWidth[scheduleEntry.id]?.width || 0
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
    left: left + '%',
    right: 100 - width - left + '%',
  }
}

function vuetifyLayout(scheduleEntries, day, times) {
  // Work around vite limitations with importing vuetify helpers in main thread vs. worker
  const parseEvent = vuetifyEvents?.parseEvent || vuetifyEventsInMainThread?.parseEvent
  const layouter = vuetifyLayouter?.column || vuetifyLayouterInMainThread?.column

  const dayStart = dayjs.utc(day.start).hour(times[0][0])
  const events = scheduleEntries
    .map((entry) => ({
      ...entry,
      startTimestamp: utcStringToTimestamp(entry.start),
      endTimestamp: utcStringToTimestamp(entry.end),
      timed: true,
    }))
    .map((evt, index) =>
      parseEvent(evt, index, 'startTimestamp', 'endTimestamp', true, false)
    )
  return keyBy(
    layouter(
      events, // schedule entries in vuetify format
      -1, // we don't want to reset the grouping on any weekday
      60 // threshold for allowed overlap between two schedule entries before they stack next to each other
    )(
      {
        year: dayStart.year(),
        month: dayStart.month(),
        day: dayStart.day(),
        hour: dayStart.hour(),
        minute: dayStart.minute(),
      }, // day start timestamp object
      events,
      true, // timed true, we don't want all-day events
      false // categoryMode false, we use calendar type 'week', not 'category'
    ),
    'event.input.id'
  )
}

function DayColumn({ times, scheduleEntries, day, styles }) {
  const relevantScheduleEntries = filterScheduleEntriesByDay(scheduleEntries, day, times)
  const leftAndWidth = vuetifyLayout(relevantScheduleEntries, day, times)

  return (
    <View style={{ ...picassoStyles.dayColumn, ...styles }}>
      <View style={picassoStyles.dayGrid}>
        {times.slice(0, times.length - 1).map(([time, weight], index) => (
          <View
            key={time}
            style={{
              ...picassoStyles.dayGridRow,
              flexGrow: weight,
              ...(index % 2 === 0 ? { backgroundColor: 'lightgrey' } : {}),
            }}
          />
        ))}
      </View>
      <View style={picassoStyles.scheduleEntryContainer}>
        {relevantScheduleEntries.map((scheduleEntry) => {
          return (
            <ScheduleEntry
              key={scheduleEntry.id}
              scheduleEntry={scheduleEntry}
              styles={{
                ...scheduleEntryPositionStyles(scheduleEntry, day, times, leftAndWidth),
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
