import {
  dateRange,
  rangeLongEnd,
  rangeShort,
  timeDurationShort,
} from '../dateHelperUTCFormatted.js'

const tcMock = (string, _, obj = {}) => {
  return Object.entries(obj).reduce((previous, [key, value]) => {
    return previous.replace(`{${key}}`, value)
  }, tcMockString(string))
}

const tcMockString = (string) => {
  switch (string) {
    case 'global.datetime.dateLong':
      return 'dd L'
    case 'global.datetime.dateShort':
      return 'dd D.M.'
    case 'global.datetime.dateTimeLong':
      return 'dd L HH:mm'
    case 'global.datetime.hourLong':
      return 'HH:mm'
    case 'global.datetime.hourShort':
      return 'H:mm'
    case 'global.datetime.duration.minutesOnly':
      return '{minutes}min'
    case 'global.datetime.duration.hoursOnly':
      return '{hours}h'
    case 'global.datetime.duration.hoursAndMinutes':
      return '{hours}h {minutes}min'
  }
}

describe('timeDurationShort', function () {
  it.each([
    ['only hour(s)', '1h', '2020-06-07T10:00:00.000Z', '2020-06-07T11:00:00.000Z'],
    ['only minute(s)', '30min', '2020-06-07T10:00:00.000Z', '2020-06-07T10:30:00.000Z'],
    ['both', '1h 30min', '2020-06-07T10:00:00.000Z', '2020-06-07T11:30:00.000Z'],
    ['both', '8h', '2020-06-07T10:00:00.000Z', '2020-06-07T18:00:00.000Z'],
    ['both', '25h 30min', '2020-06-07T10:00:00.000Z', '2020-06-08T11:30:00.000Z'],
  ])('should print %s: %s', (_, duration, start, end) => {
    expect(timeDurationShort(start, end, tcMock)).toEqual(duration)
  })
})

describe('rangeShort', () => {
  it('omits end date if it is the same as start date: Tu 1.1. 20:00 - 22:00', () => {
    expect(
      rangeShort('2019-01-01T20:00:00.000Z', '2019-01-01T22:00:00.000Z', tcMock)
    ).toEqual('Tu 1.1. 20:00 - 22:00')
  })
  it('prints end date if it another date: Tu 1.1. 14:00 - We 2.1. 10:00', () => {
    expect(
      rangeShort('2019-01-01T14:00:00.000Z', '2019-01-02T10:00:00.000Z', tcMock)
    ).toEqual('Tu 1.1. 14:00 - We 2.1. 10:00')
  })
})

describe('rangeLongEnd', () => {
  it('omits end date if it is the same as start date: 20:00 - 22:00', () => {
    expect(
      rangeLongEnd('2019-01-01T20:00:00.000Z', '2019-01-01T22:00:00.000Z', tcMock)
    ).toEqual('20:00 - 22:00')
  })
  it('prints end date if it another date: 14:00 - We 2.1. 10:00', () => {
    expect(
      rangeLongEnd('2019-01-01T14:00:00.000Z', '2019-01-02T10:00:00.000Z', tcMock)
    ).toEqual('14:00 - We 2.1. 10:00')
  })
})

describe('dateRange', () => {
  it('omits end date if it is the same as start date: Tu 01/01/2019', () => {
    expect(
      dateRange('2019-01-01T20:00:00.000Z', '2019-01-01T22:00:00.000Z', tcMock)
    ).toEqual('Tu 01/01/2019')
  })
  it('prints end date if it another date: Tu 1.1. - We 01/02/2019', () => {
    expect(
      dateRange('2019-01-01T14:00:00.000Z', '2019-01-02T10:00:00.000Z', tcMock)
    ).toEqual('Tu 1.1. - We 01/02/2019')
  })
})
