import { describe, expect, it } from "vitest";
import userInitials from '../userInitials.js'

describe('userInitials', () => {
  it.each([
    [{}, ''],
    [null, ''],
    [undefined, ''],
    [{ id: 'fffffff' }, ''],
    [{ displayName: 'test' }, 'TE'],
    [{ displayName: 'test', _meta: {} }, 'TE'],
    [{ _meta: { loading: true } }, ''],
    [{ abbreviation: 'V3', displayName: 'test' }, 'V3'],
  ])('maps %o to "%s"', (input, expected) => {
    expect(userInitials(input)).toEqual(expected)
  })
})
