import { expect, it } from 'vitest'
import { arrange } from '../scheduleEntryLayout.js'

it('should handle empty list', () => {
  expect(arrange([])).toEqual([])
})
it('should arrange schedule entries in columns', () => {
  const p0 = {
    id: '0',
    start: '2019-02-13 07:30',
    end: '2019-02-13 08:30',
  }
  const p1 = {
    id: '1',
    start: '2019-02-13 08:00',
    end: '2019-02-13 09:00',
  }
  const p2 = {
    id: '2',
    start: '2019-02-13 09:00',
    end: '2019-02-13 10:00',
  }
  const p3 = {
    id: '3',
    start: '2019-02-10 00:00',
    end: '2019-02-13 07:00',
  }
  const p4 = {
    id: '4',
    start: '2019-02-13 07:00',
    end: '2019-02-13 10:00',
  }
  const p5 = {
    id: '5',
    start: '2019-02-13 08:00',
    end: '2019-02-13 12:00',
  }

  expect(arrange([p0, p1, p2, p3, p4, p5])).toMatchObject([
    { id: '3', left: 0, width: 100 },
    { id: '4', left: 0, width: 25 },
    { id: '0', left: 25, width: 25 },
    { id: '5', left: 50, width: 25 },
    { id: '1', left: 75, width: 25 },
    { id: '2', left: 25, width: 25 },
  ])
})
