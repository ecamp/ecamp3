import { default as dayjs, dayjsLocaleMap } from '../../dayjs'
import parseTime from '../parseTime'

describe('parseTime', () => {
  describe.each(Object.keys(dayjsLocaleMap))('`%s` locale', (locale) => {
    beforeEach(() => {
      dayjs.locale(locale)
    })

    it.each([
      ['00:00', '00:00'],
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
      ['16:30', '16:30'],
      ['1', '01:00'],
      ['12', '12:00'],
      ['123', '01:23'],
      ['1234', '12:34'],
      ['13567', '13:56'],
      ['3.56', '03:56'],
      ['23,4', '23:04'],
      ['1,30', '01:30'],
      ['1:2', '01:02'],
      ['1.2', '01:02'],
      ['1,2', '01:02'],
      ['1-2', '01:02'],
      ['1;2', '01:02'],
    ])(`parses %s to %s`, (input, output) => {
      const { isValid, parsedDateTime } = parseTime(input)
      expect(parsedDateTime.format('HH:mm')).toEqual(output)
      expect(isValid).toEqual(true)
    })
  })
})
