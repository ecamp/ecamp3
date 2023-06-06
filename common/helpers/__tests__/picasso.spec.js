import { splitDaysIntoPages, calculateBedtime } from '../picasso.js'
import dayjs from '@/common/helpers/dayjs.js'

describe('splitPicassoIntoPages', () => {
  it.each([
    [[[], 1], []],
    [[[], 8], []],
    [[[], 0], []],
    [
      [[1, 2, 3], 2],
      [[1, 2], [3]]
    ],
    [
      [[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15], 8],
      [[1, 2, 3, 4, 5, 6, 7, 8], [9, 10, 11, 12, 13, 14, 15]]
    ],
    [
      [[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14], 4],
      [
        [1, 2, 3, 4],
        [5, 6, 7, 8],
        [9, 10, 11],
        [12, 13, 14]
      ]
    ]
  ])('maps %p to %p', (input, expected) => {
    expect(splitDaysIntoPages(...input)).toEqual(expected)
  })
})

describe('calculateBedtime', () => {
  it.each([
    ['no schedule entries', [[], dayjs, 1], { getUpTime: 0, bedtime: 24 }],
    [
      'single schedule entry',
      [[{ start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T10:00:00+00:00' }], dayjs, 1],
      { getUpTime: 7, bedtime: 11 },
    ],
    [
      'single schedule entry, not ending on full hours',
      [[{ start: '2022-01-02T08:15:00+00:00', end: '2022-01-02T09:35:00+00:00' }], dayjs, 1],
      { getUpTime: 7, bedtime: 10 },
    ],
    [
      'two schedule entries, forcing the gap to end before the first schedule entry even though there is a larger gap during the day',
      [[
        { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T10:00:00+00:00' },
        { start: '2022-01-02T20:00:00+00:00', end: '2022-01-02T23:00:00+00:00' },
      ], dayjs, 1],
      { getUpTime: 7, bedtime: 24 }
    ],
    [
      'schedule entry across midnight',
      [[
        { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T17:00:00+00:00' },
        { start: '2022-01-02T20:00:00+00:00', end: '2022-01-02T23:00:00+00:00' },
        { start: '2022-01-02T23:00:00+00:00', end: '2022-01-03T02:00:00+00:00' },
      ], dayjs, 1],
      { getUpTime: 7, bedtime: 27 }
    ],
    [
      'schedule entry through the night, end is not cut off',
      [[
        { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T17:00:00+00:00' },
        { start: '2022-01-02T20:00:00+00:00', end: '2022-01-02T23:00:00+00:00' },
        { start: '2022-01-02T23:00:00+00:00', end: '2022-01-03T06:00:00+00:00' },
      ], dayjs, 1],
      { getUpTime: 5, bedtime: 24 }
    ],
    [
      '24hour schedule entry does not block whole day',
      [[
        { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T17:00:00+00:00' },
        { start: '2022-01-02T20:00:00+00:00', end: '2022-01-02T23:00:00+00:00' },
        { start: '2022-01-02T23:00:00+00:00', end: '2022-01-03T02:00:00+00:00' },
        { start: '2022-01-03T12:00:00+00:00', end: '2022-01-04T12:00:00+00:00' },
      ], dayjs, 1],
      { getUpTime: 7, bedtime: 27 }
    ]
  ])('%p', (title, input, expected) => {
    expect(calculateBedtime(...input)).toEqual(expected)
  })
})
