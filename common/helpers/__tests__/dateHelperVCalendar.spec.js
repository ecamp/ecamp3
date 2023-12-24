import dayjs from 'dayjs'
import { timestampToUtcString, utcStringToTimestamp } from '../dateHelperVCalendar'

const dateTimesToTest = [
  dayjs.utc('2022-01-01T16:40'),
  dayjs.utc('2023-03-26T02:30'),
  dayjs.utc('2023-10-29T02:30'),
  dayjs.utc('2023-06-04T00:00'),
  dayjs.utc('2023-01-01T23:59'),
  dayjs.utc('2023-06-15T12:00'),
]

const parametersToTest = dateTimesToTest.map((dateTime) => ({
  epochMillis: dateTime.valueOf(),
  utcString: dateTime.format(),
}))

describe('timestampToUtcString', () => {
  it.each(parametersToTest)(
    'converts a timestamp $epochMillis into $utcString',
    ({ epochMillis, utcString }) => {
      expect(timestampToUtcString(epochMillis)).toEqual(utcString)
    }
  )
})

describe('utcStringToTimestamp', () => {
  it.each(parametersToTest)(
    'converts a utcString $utcString into epochMillis $epochMillis',
    ({ epochMillis, utcString }) => {
      expect(utcStringToTimestamp(utcString)).toEqual(epochMillis)
    }
  )
})
