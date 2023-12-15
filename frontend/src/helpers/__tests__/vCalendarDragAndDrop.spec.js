import {
  toTime,
  roundTimeToNearestQuarterHour,
  roundTimeUpToNextQuarterHour,
} from '@/helpers/vCalendarDragAndDrop.js'

describe('toTime', () => {
  it.each([
    [
      {
        date: '2023-05-08',
        time: '14:13',
        year: 2023,
        month: 5,
        day: 8,
        weekday: 1,
        hour: 14,
        minute: 13,
        hasDay: true,
        hasTime: true,
        past: false,
        present: false,
        future: true,
      },
      1683508380000,
    ],
    [
      {
        date: '2023-05-07',
        time: '10:20',
        year: 2023,
        month: 5,
        day: 7,
        weekday: 0,
        hour: 10,
        minute: 20,
        hasDay: true,
        hasTime: true,
        past: false,
        present: false,
        future: true,
      },
      1683408000000,
    ],
    [
      {
        date: '2022-04-05',
        time: '22:22',
        year: 2022,
        month: 4,
        day: 5,
        weekday: 5,
        hour: 22,
        minute: 22,
        hasDay: true,
        hasTime: true,
        past: false,
        present: false,
        future: true,
      },
      1649150520000,
    ],
  ])('maps %p to %p', (input, expected) => {
    expect(toTime(input)).toEqual(expected)
  })
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
