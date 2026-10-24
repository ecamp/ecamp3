import { describe, expect, it } from 'vitest'
import greaterThan, { validationMessageKey } from '@/plugins/veeValidate/greaterThan'
import mockI18n from './mockI18n.js'

describe('greaterThan validation', () => {
  it.each([
    [[1, { min: 0 }], true],
    [['1', { min: 0 }], true],
    [[0, { min: 0 }], validationMessageKey],
    [[0.0001, { min: 0 }], true],
    [['0.0001', { min: 0 }], true],
    [[-0.0001, { min: 0 }], validationMessageKey],
    [['-0.0001', { min: 0 }], validationMessageKey],
    [[-0, { min: 0 }], validationMessageKey],
    [[-1, { min: 0 }], validationMessageKey],
    [[1e-10, { min: 0 }], true],
    [[-1e-10, { min: 0 }], validationMessageKey],
    [['not a number', { min: 0 }], validationMessageKey],
    [['', { min: 0 }], true],
    [[null, { min: 0 }], true],
    [[undefined, { min: 0 }], true],
  ])('validates %p as %p', (input, expected) => {
    // given
    const rule = greaterThan(mockI18n)

    // when
    const [value, opts] = input
    const result = rule(value, [opts.min], { label: 'Field' })

    // then
    expect(result).toBe(expected)
  })
})
