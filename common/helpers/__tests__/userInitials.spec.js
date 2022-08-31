import userInitials from '../userInitials.js'

describe('userInitials', () => {
  it.each([
    [{ id: 'fffffff' }, ''],
    [{ displayName: 'test' }, 'TE'],
    [{ displayName: 'test', _meta: {} }, 'TE'],
    [{ _meta: { loading: true } }, ''],
  ])('maps %p to %p', (input, expected) => {
    expect(userInitials(input)).toEqual(expected)
  })
})
