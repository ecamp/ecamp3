import userLegalName from '../userLegalName.js'

describe('userLegalName', () => {
  it.each([
    [{ id: 'fffffff' }, ''],
    [{ legalName: 'test' }, 'test'],
    [{ legalName: 'test', _meta: {} }, 'test'],
    [{ _meta: { loading: true } }, ''],
  ])('maps %p to %p', (input, expected) => {
    expect(userLegalName(input)).toEqual(expected)
  })
})
