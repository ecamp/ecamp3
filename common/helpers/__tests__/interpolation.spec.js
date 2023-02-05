import { lerp, clamp, invlerp, range } from '../interpolation.js'

describe('lerp', () => {
  it.each([
    [[0, 50, 0.5], 25],
    [[1, 3, 0.5], 2],
    [[0, 5, 0.2], 1],
    [[2, 7, 1], 7],
    [[8, 4, 0.5], 6],
    [[5, 4, 1], 4],
    [[3, 7, 2], 11],
    [[2, 3, 3], 5],
    [[-8, -4, 0.5], -6],
    [[-2, -4, 0.25], -2.5],
    [[0, 10, 0.1], 1],
    [[10, 50, 1], 50],
  ])('maps %p to %p', (input, expected) => {
    expect(lerp(...input)).toEqual(expected)
  })
})

describe('clamp', () => {
  it.each([
    [[0.5, 0, 50], 0.5],
    [[3, 0, 5], 3],
    [[2, -5, 10], 2],
    [[8, 1, 3], 3],
    [[5, -1, 2], 2],
    [[3, 5, 10], 5],
    [[1, 2, 3], 2],
    [[11, 0, 10], 10],
    [[1, 10, 50], 10],
  ])('maps %p to %p', (input, expected) => {
    expect(clamp(...input)).toEqual(expected)
  })
})

describe('invlerp', () => {
  it.each([
    [[0, 2, 1], 0.5],
    [[-10, 0, -5], 0.5],
    [[-10, 10, 8], 0.9],
    [[3, 7, 5], 0.5],
    [[-1, 1, 10], 1],
    [[99, 101, 42], 0],
    [[-100, 100, -100], 0],
  ])('maps %p to %p', (input, expected) => {
    expect(invlerp(...input)).toEqual(expected)
  })
})

describe('range', () => {
  it.each([
    [[0, 1, 10, 20, 0.5], 15],
    [[10, 0, 20, 40, 7.5], 25],
    [[-10, 10, 8, 96, 5], 74],
    [[16, 32, 8, 14, 24], 11],
    [[-100, 100, 0, 100, 0], 50],
    [[-100, 100, 0, 100, 50], 75],
    [[42, 42, 0, 100, 42], NaN],
    [[1337, 50, 0, 100, 42], 100],
  ])('maps %p to %p', (input, expected) => {
    expect(range(...input)).toEqual(expected)
  })
})
