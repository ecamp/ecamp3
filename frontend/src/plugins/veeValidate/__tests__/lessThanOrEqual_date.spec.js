import lessThanOrEqual_date from '../lessThanOrEqual_date.js'
import dayjs from '@/common/helpers/dayjs.js'

const mockI18n = {
  $tc: (key) => key,
}

describe('lessThanOrEqual_date validation', () => {
  describe('german', () => {
    beforeEach(() => {
      dayjs.locale('de')
    })

    it.each([
      [['18.01.2020', { max: '19.01.2020' }], true],
      [['19.01.2020', { max: '19.01.2020' }], true],
      [['20.01.2020', { max: '19.01.2020' }], false],
      [['', { max: '19.01.2020' }], false],
      [['2.1.2020', { max: '02.01.2020' }], false], // invalid date
      [['3.1.2020', { max: '02.01.2020' }], false], // invalid date
      [['today', { max: '02.01.2020' }], false], // invalid date
    ])('validates %p as %p', (input, expected) => {
      // given
      const rule = lessThanOrEqual_date(dayjs, mockI18n)

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
      [['01/18/2020', { max: '01/19/2020' }], true],
      [['01/19/2020', { max: '01/19/2020' }], true],
      [['01/20/2020', { max: '01/19/2020' }], false],
      [['', { max: '01/19/2020' }], false],
      [['1/2/2020', { max: '01/02/2020' }], false], // invalid date
      [['1/3/2020', { max: '01/02/2020' }], false], // invalid date
      [['today', { max: '01/02/2020' }], false], // invalid date
    ])('validates %p as %p', (input, expected) => {
      // given
      const rule = lessThanOrEqual_date(dayjs, mockI18n)

      // when
      const result = rule.validate(...input)

      // then
      expect(result).toBe(expected)
    })
  })
})
