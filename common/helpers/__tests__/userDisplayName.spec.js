import userDisplayName from '../userDisplayName.js'

describe('userDisplayName', () => {
  it.each([
    [{ id: 'fffffff' }, ''],
    [{ displayName: 'test' }, 'test'],
    [{ displayName: 'test', _meta: {} }, 'test'],
    [{ _meta: { loading: true } }, ''],
  ])('maps %p to %p', (input, expected) => {
    expect(userDisplayName(input)).toEqual(expected)
  })
})
