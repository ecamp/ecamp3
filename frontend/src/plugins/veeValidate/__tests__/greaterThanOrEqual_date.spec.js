import greaterThanOrEqual_date from '../greaterThanOrEqual_date.js'
import dayjs from '@/common/helpers/dayjs.js'

const mockI18n = {
  $tc: (key) => key,
}

describe('greaterThanOrEqual_date validation', () => {
  describe('german', () => {
    beforeEach(() => {
      dayjs.locale('de')
    })

    it.each([
      [['20.01.2020', { min: '19.01.2020' }], true],
      [['19.01.2020', { min: '19.01.2020' }], true],
      [['18.01.2020', { min: '19.01.2020' }], false],
      [['', { min: '19.01.2020' }], false],
      [['2.1.2020', { min: '02.01.2020' }], false], // invalid date
      [['1.1.2020', { min: '02.01.2020' }], false], // invalid date
      [['today', { min: '02.01.2020' }], false], // invalid date
    ])('validates %p as %p', (input, expected) => {
      // given
      const rule = greaterThanOrEqual_date(dayjs, mockI18n)

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
      [['01/20/2020', { min: '01/19/2020' }], true],
      [['01/19/2020', { min: '01/19/2020' }], true],
      [['01/18/2020', { min: '01/19/2020' }], false],
      [['', { min: '01/19/2020' }], false],
      [['1/2/2020', { min: '01/02/2020' }], false], // invalid date
      [['1/1/2020', { min: '01/02/2020' }], false], // invalid date
      [['today', { min: '01/02/2020' }], false], // invalid date
    ])('validates %p as %p', (input, expected) => {
      // given
      const rule = greaterThanOrEqual_date(dayjs, mockI18n)

      // when
      const result = rule.validate(...input)

      // then
      expect(result).toBe(expected)
    })
  })
})
