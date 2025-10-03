import { describe, expect, it } from 'vitest'
import userLegalName from '../userLegalName.js'

describe('userLegalName', () => {
  it.each([
    [{ id: 'fffffff', profile: () => ({ _meta: {} }) }, ''],
    [{ profile: () => ({ legalName: 'test', _meta: {} }) }, 'test'],
    [{ profile: () => ({ legalName: 'test', _meta: {} }), _meta: {} }, 'test'],
    [{ profile: () => ({ _meta: {} }), _meta: { loading: true } }, ''],
    [{ profile: () => ({ _meta: { loading: true } }) }, ''],
  ])('maps %p to %p', (input, expected) => {
    expect(userLegalName(input)).toEqual(expected)
  })
})
