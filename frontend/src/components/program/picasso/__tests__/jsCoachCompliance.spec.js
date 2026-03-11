import { describe, expect, it } from 'vitest'
import {
  DEFAULT_JS_COMPLIANCE_DAYTIMES,
  DEFAULT_JS_COMPLIANCE_CATEGORY_PREFIXES,
  getJsCoachDayStatus,
  JS_COACH_DAY_STATUS,
} from '../jsCoachCheck.js'

function entry({ start, end, categoryShort }) {
  return {
    start,
    end,
    activity: () => ({
      category: () => ({
        short: categoryShort,
      }),
    }),
  }
}

describe('getJsCoachDayStatus', () => {
  it('returns green when all rules are met', () => {
    const scheduleEntries = [
      entry({
        start: '2025-07-10T07:00:00Z',
        end: '2025-07-10T09:00:00Z',
        categoryShort: 'LS',
      }),
      entry({
        start: '2025-07-10T13:00:00Z',
        end: '2025-07-10T15:00:00Z',
        categoryShort: 'LS',
      }),
    ]

    expect(getJsCoachDayStatus('2025-07-10', scheduleEntries)).toBe(
      JS_COACH_DAY_STATUS.GREEN
    )
  })

  it('returns yellow when time is met but only one daytime is covered', () => {
    const scheduleEntries = [
      entry({
        start: '2025-07-10T06:00:00Z',
        end: '2025-07-10T08:00:00Z',
        categoryShort: 'LS',
      }),
      entry({
        start: '2025-07-10T08:00:00Z',
        end: '2025-07-10T10:00:00Z',
        categoryShort: 'LA',
      }),
    ]

    expect(getJsCoachDayStatus('2025-07-10', scheduleEntries)).toBe(
      JS_COACH_DAY_STATUS.YELLOW
    )
  })

  it('returns red when the required time is not met', () => {
    const scheduleEntries = [
      entry({
        start: '2025-07-10T07:00:00Z',
        end: '2025-07-10T08:00:00Z',
        categoryShort: 'LS',
      }),
      entry({
        start: '2025-07-10T13:00:00Z',
        end: '2025-07-10T14:00:00Z',
        categoryShort: 'LS',
      }),
    ]

    expect(getJsCoachDayStatus('2025-07-10', scheduleEntries)).toBe(
      JS_COACH_DAY_STATUS.RED
    )
  })

  it('caps LA contribution at 2h, but does not fail if there is enough LS time', () => {
    const scheduleEntries = [
      entry({
        start: '2025-07-10T06:00:00Z',
        end: '2025-07-10T09:00:00Z',
        categoryShort: 'LA',
      }),
      entry({
        start: '2025-07-10T13:00:00Z',
        end: '2025-07-10T15:00:00Z',
        categoryShort: 'LS',
      }),
    ]

    expect(getJsCoachDayStatus('2025-07-10', scheduleEntries)).toBe(
      JS_COACH_DAY_STATUS.GREEN
    )
  })

  it('ignores activities shorter than 30 minutes', () => {
    const scheduleEntries = [
      entry({
        start: '2025-07-10T07:00:00Z',
        end: '2025-07-10T07:20:00Z',
        categoryShort: 'LS',
      }),
      entry({
        start: '2025-07-10T13:00:00Z',
        end: '2025-07-10T17:00:00Z',
        categoryShort: 'LS',
      }),
    ]

    expect(getJsCoachDayStatus('2025-07-10', scheduleEntries)).toBe(
      JS_COACH_DAY_STATUS.RED
    )
  })

  it('uses configurable daytime windows', () => {
    const scheduleEntries = [
      entry({
        start: '2025-07-10T08:00:00Z',
        end: '2025-07-10T10:00:00Z',
        categoryShort: 'LS',
      }),
      entry({
        start: '2025-07-10T10:00:00Z',
        end: '2025-07-10T12:00:00Z',
        categoryShort: 'LS',
      }),
    ]

    expect(getJsCoachDayStatus('2025-07-10', scheduleEntries)).toBe(
      JS_COACH_DAY_STATUS.YELLOW
    )

    expect(
      getJsCoachDayStatus('2025-07-10', scheduleEntries, {
        ...DEFAULT_JS_COMPLIANCE_DAYTIMES,
        morning: { start: '08:00', end: '09:00' },
        afternoon: { start: '09:00', end: '18:00' },
      })
    ).toBe(JS_COACH_DAY_STATUS.GREEN)
  })

  it('uses configurable category prefixes', () => {
    const scheduleEntries = [
      entry({
        start: '2025-07-10T07:00:00Z',
        end: '2025-07-10T09:00:00Z',
        categoryShort: 'S',
      }),
      entry({
        start: '2025-07-10T13:00:00Z',
        end: '2025-07-10T15:00:00Z',
        categoryShort: 'S',
      }),
    ]

    // Should be red without custom prefixes (S is not LS or LA)
    expect(getJsCoachDayStatus('2025-07-10', scheduleEntries)).toBe(
      JS_COACH_DAY_STATUS.RED
    )

    // Should be green with custom prefixes (S is included)
    expect(
      getJsCoachDayStatus('2025-07-10', scheduleEntries, DEFAULT_JS_COMPLIANCE_DAYTIMES, {
        ls: 'S;LS',
        la: 'A',
      })
    ).toBe(JS_COACH_DAY_STATUS.GREEN)
  })

  it('respects LA prefix configuration separately', () => {
    const scheduleEntries = [
      entry({
        start: '2025-07-10T06:00:00Z',
        end: '2025-07-10T09:00:00Z',
        categoryShort: 'A',
      }),
      entry({
        start: '2025-07-10T13:00:00Z',
        end: '2025-07-10T15:00:00Z',
        categoryShort: 'S',
      }),
    ]

    // With custom prefixes where A is LA category and S is JS category
    expect(
      getJsCoachDayStatus('2025-07-10', scheduleEntries, DEFAULT_JS_COMPLIANCE_DAYTIMES, {
        ls: 'S',
        la: 'A',
      })
    ).toBe(JS_COACH_DAY_STATUS.GREEN)
  })

  it('supports multiple semicolon-separated prefixes like LS;S and LA;A;LSA', () => {
    const scheduleEntries = [
      entry({
        start: '2025-07-10T07:00:00Z',
        end: '2025-07-10T09:00:00Z',
        categoryShort: 'S',
      }),
      entry({
        start: '2025-07-10T13:00:00Z',
        end: '2025-07-10T15:00:00Z',
        categoryShort: 'LSA',
      }),
    ]

    expect(
      getJsCoachDayStatus('2025-07-10', scheduleEntries, DEFAULT_JS_COMPLIANCE_DAYTIMES, {
        ls: 'LS;S',
        la: 'LA;A;LSA',
      })
    ).toBe(JS_COACH_DAY_STATUS.GREEN)
  })

  it('normalizes prefixes to uppercase and ignores identifiers longer than 8 characters', () => {
    const scheduleEntries = [
      entry({
        start: '2025-07-10T07:00:00Z',
        end: '2025-07-10T09:00:00Z',
        categoryShort: 'ls',
      }),
      entry({
        start: '2025-07-10T13:00:00Z',
        end: '2025-07-10T15:00:00Z',
        categoryShort: 'lagerakt',
      }),
    ]

    expect(
      getJsCoachDayStatus('2025-07-10', scheduleEntries, DEFAULT_JS_COMPLIANCE_DAYTIMES, {
        ls: ' ls ; identifier-too-long ',
        la: 'lagerakt;also-too-long',
      })
    ).toBe(JS_COACH_DAY_STATUS.GREEN)
  })
})
