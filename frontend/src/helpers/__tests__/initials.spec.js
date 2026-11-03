import { describe, expect, it } from 'vitest'
import initials from '../initials.js'

describe('initials', () => {
  it.each([
    [undefined, ''],
    [null, ''],
    ['', ''],
    [' ', ''],
    ['  ', ''],
    ['.,_', ''],
    [':)', ':)'],
    ['a', 'A'],
    ['ab', 'AB'],
    ['Ab', 'AB'],
    ['aB', 'AB'],
    ['Regulus Arcturus Black', 'RA'],
    ['luke skywalker', 'LS'],
    ['Luke', 'LU'],
    ['eCamp', 'EC'],
    ['Bi-Pi', 'BP'],
    ['bi-pi@scouts.com', 'BP'],
    ['ecamp@scouts.com', 'EC'],
    ['Happy 😊', 'H😊'],
    ['Ömer Çedille', 'ÖÇ'],
  ])('maps %p to %p', (input, expected) => {
    expect(initials(input)).toEqual(expected)
  })
})
