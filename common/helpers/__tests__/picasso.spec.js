import { splitDaysIntoPages } from '../picasso.js'

describe('splitPicassoIntoPages', () => {
  it.each([
    [[[], 1], []],
    [[[], 8], []],
    [[[], 0], []],
    [
      [[1, 2, 3], 2],
      [{ days: [1, 2] }, { days: [3] }],
    ],
    [
      [[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15], 8],
      [{ days: [1, 2, 3, 4, 5, 6, 7, 8] }, { days: [9, 10, 11, 12, 13, 14, 15] }],
    ],
    [
      [[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14], 4],
      [
        { days: [1, 2, 3, 4] },
        { days: [5, 6, 7, 8] },
        { days: [9, 10, 11] },
        { days: [12, 13, 14] },
      ],
    ],
  ])('maps %p to %p', (input, expected) => {
    expect(splitDaysIntoPages(...input)).toEqual(expected)
  })
})
