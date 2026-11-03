import { describe, expect, it } from "vitest";
import { splitDaysIntoPages, calculateBedtime } from '../picasso.js'
import dayjs from '@/common/helpers/dayjs.js'

describe('splitPicassoIntoPages', () => {
  function daysBetween(start, end) {
    // Generates an array [start, start + 1, start + 2, ..., end - 1, end]
    return [...Array(end - start + 1).keys()].map((day) => day + start)
  }

  it.each([
    [[[], 1], []],
    [[[], 8], []],
    [[[], 0], []],
    [
      [daysBetween(1, 3), 2],
      [daysBetween(1, 2), daysBetween(3, 3)],
    ],
    [
      [daysBetween(1, 15), 8],
      [daysBetween(1, 8), daysBetween(9, 15)],
    ],
    [
      [daysBetween(1, 14), 4],
      [daysBetween(1, 4), daysBetween(5, 8), daysBetween(9, 11), daysBetween(12, 14)],
    ],
  ])('maps %p to %p', (input, expected) => {
    expect(splitDaysIntoPages(...input)).toEqual(expected)
  })
})

describe('calculateBedtime', () => {
  it.each([
    [
      'no schedule entries',
      [
        [],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-03T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 0, bedtime: 24 },
    ],
    [
      'single schedule entry',
      [
        [{ start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T10:00:00+00:00' }],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-03T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 8, bedtime: 10 },
    ],
    [
      'single schedule entry, not ending on full hours',
      [
        [{ start: '2022-01-02T08:15:00+00:00', end: '2022-01-02T09:35:00+00:00' }],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-03T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 8, bedtime: 10 },
    ],
    [
      'two schedule entries, forcing the gap to end before the first schedule entry even though there is a larger gap during the day',
      [
        [
          { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T10:00:00+00:00' },
          { start: '2022-01-02T20:00:00+00:00', end: '2022-01-02T23:00:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-03T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 8, bedtime: 23 },
    ],
    [
      'schedule entry across midnight',
      [
        [
          { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T17:00:00+00:00' },
          { start: '2022-01-02T20:00:00+00:00', end: '2022-01-02T23:00:00+00:00' },
          { start: '2022-01-02T23:00:00+00:00', end: '2022-01-03T02:00:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-04T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 8, bedtime: 26 },
    ],
    [
      'schedule entry through the night, end is not cut off',
      [
        [
          { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T17:00:00+00:00' },
          { start: '2022-01-02T20:00:00+00:00', end: '2022-01-02T23:00:00+00:00' },
          { start: '2022-01-02T23:00:00+00:00', end: '2022-01-03T06:00:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-04T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 5, bedtime: 24 },
    ],
    [
      '24hour schedule entry does not block whole day',
      [
        [
          { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T17:00:00+00:00' },
          { start: '2022-01-02T20:00:00+00:00', end: '2022-01-02T23:00:00+00:00' },
          { start: '2022-01-02T23:00:00+00:00', end: '2022-01-03T02:00:00+00:00' },
          { start: '2022-01-03T12:00:00+00:00', end: '2022-01-04T12:00:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-05T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 8, bedtime: 26 },
    ],
    [
      'schedule entry starting at bedtime on a full hour moves the bedtime later, to avoid hiding the schedule entry completely',
      [
        [
          { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T22:00:00+00:00' },
          { start: '2022-01-02T22:00:00+00:00', end: '2022-01-03T08:30:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-04T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 8, bedtime: 23 },
    ],
    [
      'schedule entry starting around bedtime on a half hour does not move the bedtime, because it is already visible enough',
      [
        [
          { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T22:00:00+00:00' },
          { start: '2022-01-02T21:30:00+00:00', end: '2022-01-03T08:30:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-04T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 8, bedtime: 22 },
    ],
    [
      'schedule entry starting around bedtime on a quarter hour moves the bedtime later, to make sure it is visible for at least half an hour',
      [
        [
          { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T22:00:00+00:00' },
          { start: '2022-01-02T21:45:00+00:00', end: '2022-01-03T08:30:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-04T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 8, bedtime: 23 },
    ],
    [
      'very short schedule entry starting around bedtime does not move the bedtime later, because it would not gain more visibility',
      [
        [
          { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T22:00:00+00:00' },
          { start: '2022-01-02T21:45:00+00:00', end: '2022-01-02T22:00:00+00:00' },
          { start: '2022-01-02T20:00:00+00:00', end: '2022-01-03T08:30:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-04T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 8, bedtime: 22 },
    ],
    [
      'schedule entry ending at getUpTime on a full hour moves the getUpTime earlier, to avoid hiding the schedule entry completely',
      [
        [
          { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T22:00:00+00:00' },
          { start: '2022-01-02T21:30:00+00:00', end: '2022-01-03T08:00:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-04T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 7, bedtime: 22 },
    ],
    [
      'schedule entry ending around getUpTime on a half hour does not move the getUpTime, because it is already visible enough',
      [
        [
          { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T22:00:00+00:00' },
          { start: '2022-01-02T21:30:00+00:00', end: '2022-01-03T08:30:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-04T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 8, bedtime: 22 },
    ],
    [
      'schedule entry ending around getUpTime on a quarter hour moves the getUpTime earlier, to make sure it is visible for at least half an hour',
      [
        [
          { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T22:00:00+00:00' },
          { start: '2022-01-02T21:30:00+00:00', end: '2022-01-03T08:15:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-04T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 7, bedtime: 22 },
    ],
    [
      'very short schedule entry ending around getUpTime does not move the getUpTime earlier, because it would not gain more visibility',
      [
        [
          { start: '2022-01-02T08:00:00+00:00', end: '2022-01-02T22:00:00+00:00' },
          { start: '2022-01-03T08:00:00+00:00', end: '2022-01-03T08:15:00+00:00' },
          { start: '2022-01-02T21:30:00+00:00', end: '2022-01-03T09:00:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-04T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 8, bedtime: 22 },
    ],
    [
      'empty first day, schedule entries only towards end of last day, latestGetUpTime falls back to a sensible value',
      [
        [
          { start: '2022-01-03T18:00:00+00:00', end: '2022-01-03T19:00:00+00:00' },
          { start: '2022-01-03T20:00:00+00:00', end: '2022-01-03T21:00:00+00:00' },
          { start: '2022-01-03T22:00:00+00:00', end: '2022-01-03T23:00:00+00:00' },
        ],
        dayjs,
        dayjs.utc('2022-01-02T00:00:00+00:00'),
        dayjs.utc('2022-01-04T00:00:00+00:00'),
        1,
      ],
      { getUpTime: 18, bedtime: 23 },
    ],
  ])('%p', (title, input, expected) => {
    expect(calculateBedtime(...input)).toEqual(expected)
  })
})
