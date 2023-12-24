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
    [1649190120000, 1649189700000],
    [1683447600000, 1683447300000],
    [1683543980000, 1683543600000],
    [1683547980999, 1683548100000],
  ])('maps %p to %p', (input, expected) => {
    expect(roundTimeToNearestQuarterHour(input)).toEqual(expected)
  })
})

describe('roundTimeUpToNextQuarterHour', () => {
  it.each([
    [1649190120000, 1649190600000],
    [1683447600000, 1683448200000],
    [1683543980000, 1683544500000],
    [1683547980999, 1683548100000],
  ])('maps %p to %p', (input, expected) => {
    expect(roundTimeUpToNextQuarterHour(input)).toEqual(expected)
  })
})
