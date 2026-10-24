import { describe, expect, it } from 'vitest'
import {
  ysPhysicalActivityDurationLong,
  ysPhysicalActivityDurationShort,
} from '../ysPhysicalActivityDuration.js'

const translations = {
  'global.datetime.duration.minutesOnly': '{minutes} min',
  'global.datetime.duration.hoursOnly': '{hours} h',
  'global.datetime.duration.hoursAndMinutes': '{hours} h {minutes} min',
  'entity.scheduleEntry.fields.ysPhysicalActivityDurationShort': 'BZ {minutes} min',
}
const tc = (key, params) =>
  translations[key].replace(/\{(\w+)}/g, (_, param) => params[param])

describe('ysPhysicalActivityDurationLong', () => {
  it.each([
    [30, '30 min'],
    [45, '45 min'],
    [60, '1 h'],
    [75, '1 h 15 min'],
    [90, '1 h 30 min'],
    [105, '1 h 45 min'],
    [120, '2 h'],
  ])('formats %p as %p', (minutes, expected) => {
    expect(ysPhysicalActivityDurationLong(minutes, tc)).toEqual(expected)
  })

  it.each([[null], [undefined]])('returns empty string for %p', (minutes) => {
    expect(ysPhysicalActivityDurationLong(minutes, tc)).toEqual('')
  })
})

describe('ysPhysicalActivityDurationShort', () => {
  it.each([
    [30, 'BZ 30 min'],
    [60, 'BZ 60 min'],
    [120, 'BZ 120 min'],
  ])('formats %p as %p', (minutes, expected) => {
    expect(ysPhysicalActivityDurationShort(minutes, tc)).toEqual(expected)
  })

  it.each([[null], [undefined]])('returns empty string for %p', (minutes) => {
    expect(ysPhysicalActivityDurationShort(minutes, tc)).toEqual('')
  })
})
