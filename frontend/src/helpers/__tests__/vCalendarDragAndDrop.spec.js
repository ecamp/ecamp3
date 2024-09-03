import { describe, expect, it } from 'vitest'
import {
  toTime,
  roundTimeToNearestQuarterHour,
  roundTimeUpToNextQuarterHour,
} from '@/helpers/vCalendarDragAndDrop.js'
import dayjs from '@/common/helpers/dayjs.js'
import { parseDate } from 'vuetify/src/components/VCalendar/util/timestamp'

describe('toTime', () => {
  const dateTimesToTest = [
    dayjs('2023-05-08T14:13'),
    dayjs('2023-05-07T10:20'),
    dayjs('2023-04-05T22:22'),
    dayjs.utc('2023-03-26T01:30'), //daylight saving time change
    dayjs.utc('2023-10-29T01:30'), //daylight saving time change
  ]

  const parametersToTest = dateTimesToTest.map((dateTime) => ({
    calendarTimestamp: {
      ...parseDate(dateTime.toDate()),
      json: JSON.stringify(parseDate(dateTime.toDate())),
    },
    epochMillis: dateTime.valueOf(),
  }))

  it.each(parametersToTest)(
    'maps $calendarTimestamp.json to $epochMillis',
    ({ calendarTimestamp, _, epochMillis }) => {
      expect(toTime(calendarTimestamp)).toEqual(epochMillis)
    }
  )
})

describe('roundTimeToNearestQuarterHour', () => {
  it.each([
    ['22:22:00', '22:15'],
    ['22:22:31', '22:30'],
    ['10:20:00', '10:15'],
    ['13:00:20', '13:00'],
    ['14:13:00', '14:15'],
    ['23:52:31', '00:00'],
  ])('maps %s to %s', (input, expected) => {
    const epochMillis = asEpochMillis(input)
    const roundedEpoch = roundTimeToNearestQuarterHour(epochMillis)
    expect(fromEpochMillis(roundedEpoch)).toEqual(expected)
  })
})

describe('roundTimeUpToNextQuarterHour', () => {
  it.each([
    ['22:22:00', '22:30'],
    ['10:20:00', '10:30'],
    ['13:00:20', '13:15'],
    ['14:13:00', '14:15'],
  ])('maps %s to %s', (input, expected) => {
    const epochMillis = asEpochMillis(input)
    const roundedEpoch = roundTimeUpToNextQuarterHour(epochMillis)
    expect(fromEpochMillis(roundedEpoch)).toEqual(expected)
  })
})

/**
 * Converts a time string (HH:mm:ss) in local timezone into epoch millis
 *
 * @param timeStr {string} HH:mm:ss
 * @returns {number} epoch millis
 */
function asEpochMillis(timeStr) {
  return dayjs('2023-12-24')
    .hour(Number(timeStr.slice(0, 2)))
    .minute(Number(timeStr.slice(3, 5)))
    .second(Number(timeStr.slice(6, 8)))
    .valueOf()
}

/**
 * Converts a given epoch millisecond value to a formatted time string in the format 'HH:mm'.
 *
 * @param {number} epochMillis - The epoch millisecond value to be converted.
 * @return {string} The formatted time string in the format 'HH:mm'.
 */
function fromEpochMillis(epochMillis) {
  return dayjs(epochMillis).year(2023).month(12).date(24).format('HH:mm')
}
