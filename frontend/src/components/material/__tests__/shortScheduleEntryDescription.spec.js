import shortScheduleEntryDescription from '../shortScheduleEntryDescription.js'

describe('shortScheduleEntryDescription', () => {
  it.each([
    [{ _meta: { loading: true } }, ''],
    [null, ''],
    [undefined, ''],
    [
      {
        _meta: { loading: false },
        number: '1.2',
        activity: () => ({
          title: 'foo',
        }),
        dayNumber: '3',
      },
      '1.2',
    ],
    [
      {
        _meta: { loading: false },
        number: '',
        activity: () => ({
          title: 'foo',
        }),
        dayNumber: '3',
      },
      '["global.shortScheduleEntryDescription",1,{"title":"foo","dayNumber":"3"}]',
    ],
  ])('maps %p to %p', (input, expected) => {
    const tc = (...args) => JSON.stringify(args)
    expect(shortScheduleEntryDescription(input, tc)).toEqual(expected)
  })
})
