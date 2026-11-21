import { describe, expect, it } from 'vitest'
import { parsePageRange } from '../pageRange.js'

describe('parsePageRange', () => {
  it.each([
    ['', [0, 1, 2, 3, 4]],
    [undefined, [0, 1, 2, 3, 4]],
    ['   ', [0, 1, 2, 3, 4]],
    ['2', [1]],
    ['1-3', [0, 1, 2]],
    ['1-3, 5', [0, 1, 2, 4]],
    ['5, 1', [4, 0]],
    ['4-9', [3, 4]],
    ['7', []],
    ['abc', []],
  ])('parses %j', (input, expected) => {
    expect(parsePageRange(input, 5)).toEqual(expected)
  })
})
