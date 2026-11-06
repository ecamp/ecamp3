import { describe, expect, it } from 'vitest'
import {
  isYsPhysicalActivityDurationOpenEnded,
  ysPhysicalActivityDuration,
  ysPhysicalActivityDurationFormatted,
} from '../ysPhysicalActivityDuration.js'

const translations = {
  'global.datetime.duration.minutesOnly': '{minutes} min',
  'global.datetime.duration.hoursOnly': '{hours} h',
  'global.datetime.duration.hoursAndMinutes': '{hours} h {minutes} min',
  'entity.scheduleEntry.fields.ysPhysicalActivityDurationFormatted': 'BZ {duration}',
  'entity.scheduleEntry.fields.ysPhysicalActivityDurationOrMore': '{duration} oder mehr',
  'entity.scheduleEntry.fields.ysPhysicalActivityDurationOrMoreShort': '{hours}+ h',
}
const tc = (key, params) =>
  translations[key].replace(/\{(\w+)}/g, (_, param) => params[param])

describe('ysPhysicalActivityDuration', () => {
  it.each([
    [30, '30 min'],
    [45, '45 min'],
    [60, '1 h'],
    [75, '1 h 15 min'],
    [90, '1 h 30 min'],
    [105, '1 h 45 min'],
    [120, '2 h oder mehr'],
  ])('formats %p as %p', (minutes, expected) => {
    expect(ysPhysicalActivityDuration(minutes, tc)).toEqual(expected)
  })

  it.each([[null], [undefined]])('returns empty string for %p', (minutes) => {
    expect(ysPhysicalActivityDuration(minutes, tc)).toEqual('')
  })
})

describe('ysPhysicalActivityDurationFormatted', () => {
  it.each([
    [30, 'BZ 30 min'],
    [60, 'BZ 1 h'],
    [75, 'BZ 1 h 15 min'],
    [120, 'BZ 2+ h'],
  ])('formats %p as %p', (minutes, expected) => {
    expect(ysPhysicalActivityDurationFormatted(minutes, tc)).toEqual(expected)
  })

  it.each([[null], [undefined]])('returns empty string for %p', (minutes) => {
    expect(ysPhysicalActivityDurationFormatted(minutes, tc)).toEqual('')
  })
})

describe('isYsPhysicalActivityDurationOpenEnded', () => {
  it.each([
    [105, false],
    [120, true],
    [150, true],
  ])('returns %p for %p', (minutes, expected) => {
    expect(isYsPhysicalActivityDurationOpenEnded(minutes)).toEqual(expected)
  })
})
