import dayjs from '@/common/helpers/dayjs.js'

describe('formatDatePeriod dayjs plugin', () => {
  it.each([
    ['', ''],
    ['L', '03.04.2023 - 07.04.2023'],
  ])('maps %p to %p', (input, expected) => {
    const startDate = dayjs('2023-04-03 0:00')
    const endDate = dayjs('2023-04-07 0:00')
    expect(dayjs.formatDatePeriod(startDate, endDate, input[0], 'de')).toEqual(expected)
  })
})
