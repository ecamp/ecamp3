import { default as dayjs, dayjsLocaleMap } from '../../dayjs'
import parseTime from '../parseTime'
import { padStart, range } from 'lodash'
import { HTML5_FMT } from '../../dateFormat'

describe('parseTime', () => {
  describe.each(Object.keys(dayjsLocaleMap))('`%s` locale', (locale) => {
    beforeEach(() => {
      dayjs.locale(locale)
    })

    const numbersFrom1to23 = range(1, 24).map((number) => [
      `${number}`,
      `${padStart(number, 2, '0')}:00`,
    ])

    it.each([
      ['00:00', '00:00'],
      ['12:00', '12:00'],
      ['23:59', '23:59'],
      ['01:01', '01:01'],
      ['09:59', '09:59'],
      ['20:00', '20:00'],
      ['10:11', '10:11'],
      ['010:11', '10:11', { en: '00:10' }],
      ['00010:11', '10:11', { en: '00:01' }],
      ['18:30', '18:30'],
      ['001:11', '01:11', { en: '00:01' }],
      ['000001:11', '01:11', { en: '00:00' }],
      ['16:30', '16:30'],
      ...numbersFrom1to23,
      [0, '00:00'],
      [-0, '00:00'],
      ['123', '01:23'],
      ['1234', '12:34'],
      ['13567', '13:56'],
      ['407', '04:07'],
      ['3.56', '03:56'],
      ['23,4', '23:04'],
      ['1,30', '01:30'],
      ['1:2', '01:02'],
      ['1.2', '01:02'],
      ['1,2', '01:02'],
      ['1-2', '01:02'],
      ['1;2', '01:02'],
      ['01', '01:00'],
      ['18', '18:00'],
      ['23', '23:00'],
      // not specified like this, but the current behaviour
      ['010', '00:10'],
      ['023', '00:23'],
      ['145', '01:45'],
      ['159', '01:59'],
      ['200', '02:00'],
      ['214', '02:14'],
      ['313', '03:13'],
      ['659', '06:59'],
    ])(
      `parses %s to %s (but special case for %s)`,
      (input, output, specialCaseForLocale = {}) => {
        const { isValid, parsedDateTime } = parseTime(input)

        const expectedOutput =
          locale in specialCaseForLocale ? specialCaseForLocale[locale] : output

        expect(parsedDateTime.format('HH:mm')).toEqual(expectedOutput)
        // the resulting dayjs object should not flow over to the next day
        expect(parsedDateTime.format(HTML5_FMT.DATE)).toEqual(
          dayjs().format(HTML5_FMT.DATE)
        )
        expect(isValid).toEqual(true)
      }
    )

    it.each([
      [null],
      [undefined],
      [''],
      [' '],
      ['\t'],
      [[]],
      [{}],
      ['invalid time'],
      ['a very long time that the cases are in multiple lines'],
      ['24'],
      ['99'],
      ['2525'],
      ['160'],
      ['189'],
      ['191'],
      ['260'],
      ['269'],
      ['999'],
    ])(`rejects %s`, (input) => {
      const { isValid } = parseTime(input)
      expect(isValid).toEqual(false)
    })
  })
})
