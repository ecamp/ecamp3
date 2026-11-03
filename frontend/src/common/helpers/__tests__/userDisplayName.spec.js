import { describe, expect, it } from "vitest";
import userDisplayName from '../userDisplayName.js'

describe('userDisplayName', () => {
  it.each([
    [{ id: 'fffffff' }, ''],
    [{ displayName: 'test' }, 'test'],
    [{ displayName: 'test', _meta: {} }, 'test'],
    [{ _meta: { loading: true } }, ''],
    [null, ''],
    [undefined, ''],
  ])('maps %p to %p', (input, expected) => {
    expect(userDisplayName(input)).toEqual(expected)
  })
})
