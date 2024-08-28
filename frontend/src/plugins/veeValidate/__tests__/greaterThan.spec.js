import { describe, expect, it } from 'vitest'
import greaterThan from '@/plugins/veeValidate/greaterThan'

const mockI18n = {
  $tc: (key) => key,
}

describe('greaterThan validation', () => {
  it.each([
    [[1, { min: 0 }], true],
    [['1', { min: 0 }], true],
    [[0, { min: 0 }], false],
    [[0.0001, { min: 0 }], true],
    [['0.0001', { min: 0 }], true],
    [[-0.0001, { min: 0 }], false],
    [['-0.0001', { min: 0 }], false],
    [[-0, { min: 0 }], false],
    [[-1, { min: 0 }], false],
    [[1e-10, { min: 0 }], true],
    [[-1e-10, { min: 0 }], false],
    [['not a number', { min: 0 }], false],
  ])('validates %p as %p', (input, expected) => {
    // given
    const rule = greaterThan(mockI18n)

    // when
    const result = rule.validate(...input)

    // then
    expect(result).toBe(expected)
  })
})
