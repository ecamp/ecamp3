import { default as dayjs, dayjsLocaleMap } from '../../dayjs'
import parseTime from '../parseTime'

describe('parseTime', () => {
  describe.each(Object.keys(dayjsLocaleMap))('`%s` locale', (locale) => {
    beforeEach(() => {
      dayjs.locale(locale)
    })

    it.each([
      ['12:00', '12:00'],
      ['23:59', '23:59'],
      ['01:01', '01:01'],
      ['09:59', '09:59'],
      ['20:00', '20:00'],
      ['10:11', '10:11'],
      ['010:11', '10:11'],
      ['00010:11', '10:11'],
      ['18:30', '18:30'],
      ['001:11', '01:11'],
      ['000001:11', '01:11'],
    ])(`parses %s to %s`, (input, output) => {
      if (locale === 'en') {
        const parts = input.split(':')
        if (parts.length === 2) {
          const hour = parseInt(parts[0])
          if (hour >= 12) {
            const newHourNumber = hour > 12 ? hour - 12 : hour
            input = `${newHourNumber}:${parts[1]} PM`
          } else {
            input = `${hour}:${parts[1]} AM`
          }
        }
      }

      const { isValid, parsedDateTime } = parseTime(input)
      expect(parsedDateTime.format('HH:mm')).toEqual(output)
      expect(isValid).toEqual(true)
    })
  })
})
