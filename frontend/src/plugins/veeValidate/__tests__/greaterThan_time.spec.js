import greaterThan_time from '../greaterThan_time.js'
import dayjs from '@/common/helpers/dayjs.js'

const mockI18n = {
  $tc: (key) => key,
}

function convertTimeStringToDayjsObject(timeString) {
  // The date should be actively ignored by the validator, so we can use any random date
  return dayjs.utc('2022-09-03 ' + timeString, 'YYYY-MM-DD LT')
}

describe('greaterThan_time validation', () => {
  const testcases = {
    de: [
      [['09:31', { min: '09:30' }], true],
      [['09:30', { min: '09:30' }], false],
      [['09:29', { min: '09:30' }], false],
      [['', { min: '09:30' }], false],
      [['9:31 AM', { min: '09:30' }], true], // dayjs parser is somewhat forgiving here
      [['9:31', { min: '09:30' }], true],
      [['9:30', { min: '09:30' }], false],
      [['9:29', { min: '09:30' }], false],
      [['now', { min: '09:30' }], false], // invalid date
    ],
    en: [
      [['09:31 AM', { min: '09:30 AM' }], true],
      [['09:30 AM', { min: '09:30 AM' }], false],
      [['09:29 AM', { min: '09:30 AM' }], false],
      [['', { min: '09:30 AM' }], false],
      [['09:30', { min: '09:30 AM' }], false], // wrong format
      [['9:31', { min: '09:30 AM' }], false], // wrong format
      [['now', { min: '09:30 AM' }], false], // invalid date
    ],
  }

  describe.each(Object.entries(testcases))('%s', (language, cases) => {
    beforeEach(() => {
      dayjs.locale(language)
    })

    describe('when min is a string', () => {
      it.each(cases)('validates %p as %p', (input, expected) => {
        // given
        const rule = greaterThan_time(dayjs, mockI18n)

        // when
        const result = rule.validate(...input)

        // then
        expect(result).toBe(expected)
      })
    })

    describe('when min is a dayjs object', () => {
      const casesWithDayjsObjectsAsMin = cases.map(([[input, { min }], expected]) => {
        dayjs.locale(language)
        return [[input, { min: convertTimeStringToDayjsObject(min) }], expected]
      })

      it.each(casesWithDayjsObjectsAsMin)('validates %p as %p', (input, expected) => {
        // given
        const rule = greaterThan_time(dayjs, mockI18n)

        // when
        const result = rule.validate(...input)

        // then
        expect(result).toBe(expected)
      })
    })
  })
})
