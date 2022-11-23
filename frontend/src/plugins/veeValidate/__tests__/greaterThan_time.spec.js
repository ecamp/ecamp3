import greaterThan_time from '../greaterThan_time.js'
import dayjs from '@/common/helpers/dayjs.js'

const mockI18n = {
  $tc: (key) => key,
}

describe('greaterThan_time validation', () => {
  describe('german', () => {
    beforeEach(() => {
      dayjs.locale('de')
    })

    it.each([
      [['09:31', { min: '09:30' }], true],
      [['09:30', { min: '09:30' }], false],
      [['09:29', { min: '09:30' }], false],
      [['', { min: '09:30' }], false],
      [['9:31 AM', { min: '09:30' }], true], // TODO is this behaviour correct?
      [['9:31', { min: '09:30' }], true], // TODO is this behaviour correct?
      [['now', { min: '09:30' }], false], // invalid date
    ])('validates %p as %p', (input, expected) => {
      // given
      const rule = greaterThan_time(dayjs, mockI18n)

      // when
      const result = rule.validate(...input)

      // then
      expect(result).toBe(expected)
    })
  })
  describe('english', () => {
    beforeEach(() => {
      dayjs.locale('en')
    })

    it.each([
      [['09:31 AM', { min: '09:30 AM' }], true],
      [['09:30 AM', { min: '09:30 AM' }], false],
      [['09:29 AM', { min: '09:30 AM' }], false],
      [['', { min: '09:30 AM' }], false],
      [['09:30', { min: '09:30 AM' }], false], // invalid date
      [['9:30', { min: '09:30 AM' }], false], // invalid date
      [['now', { min: '09:30 AM' }], false], // invalid date
    ])('validates %p as %p', (input, expected) => {
      // given
      const rule = greaterThan_time(dayjs, mockI18n)

      // when
      const result = rule.validate(...input)

      // then
      expect(result).toBe(expected)
    })
  })
})
