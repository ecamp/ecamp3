import userLegalName from '../userLegalName.js'

describe('userLegalName', () => {
  it.each([
    [{ id: 'fffffff', profile: () => ({}) }, ''],
    [{ profile: () => ({ legalName: 'test' }) }, 'test'],
    [{ profile: () => ({ legalName: 'test' }), _meta: {} }, 'test'],
    [{ profile: () => ({}), _meta: { loading: true } }, ''],
    [{ profile: () => ({ _meta: { loading: true } }) }, ''],
  ])('maps %p to %p', (input, expected) => {
    expect(userLegalName(input)).toEqual(expected)
  })
})
