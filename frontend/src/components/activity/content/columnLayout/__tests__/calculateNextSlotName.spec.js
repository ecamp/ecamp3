import { calculateNextSlotName, adjustColumnWidths } from '../calculateNextSlotName.js'

describe('generating a next slot name', () => {
  const examples = [
    [[], '1'],
    [[''], '1'],
    [['0'], '1'],
    [['1'], '2'],
    [['a'], '1'],
    [['A'], '1'],
    [['9'], '1'],
    [['z'], '1'],
    [['Z'], '1'],
    [['0.'], '1'],
    [['9.'], '1'],
    [['999'], '1'],
    [['1', '2'], '3'],
    [['1', '3'], '2'],
    [['1z', '1'], '2'],
    [['mitte', 'oben', 'unten'], '1'],
    [['😋'], '1'],
    [['a😋'], '1'],
    [['1', '2', '3', '4', '5', '6', '7', '8', '9'], '10'],
    [['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'], '11']
  ]

  examples.forEach(([input, expected]) => {
    it(input.toString(), () => {
      expect(calculateNextSlotName(input)).toEqual(expected)
    })
  })
})

describe('adjusting the column widths', () => {
  // prettier-ignore
  const examples = [
    [[], []],
    // compacting columns
    [[{ name: '1', width: 20 }], [12]],
    [[{ name: '1', width: 12 }, { name: '2', width: 3 }], [9, 3]],
    [[{ name: '1', width: 3 }, { name: '2', width: 9 }, { name: '3', width: 3 }], [3, 6, 3]],
    [[{ name: '1', width: 4 }, { name: '2', width: 8 }, { name: '3', width: 3 }], [4, 5, 3]],
    [[{ name: '1', width: 5 }, { name: '2', width: 7 }, { name: '3', width: 3 }], [5, 4, 3]],
    [[{ name: '1', width: 6 }, { name: '2', width: 6 }, { name: '3', width: 3 }], [6, 3, 3]],
    [[{ name: '1', width: 7 }, { name: '2', width: 5 }, { name: '3', width: 3 }], [6, 3, 3]],
    [[{ name: '1', width: 8 }, { name: '2', width: 4 }, { name: '3', width: 3 }], [6, 3, 3]],
    [[{ name: '1', width: 9 }, { name: '2', width: 3 }, { name: '3', width: 3 }], [6, 3, 3]],
    [[{ name: '1', width: 3 }, { name: '2', width: 3 }, { name: '3', width: 6 }, { name: '4', width: 3 }], [3, 3, 3, 3]],
    [[{ name: '1', width: 3 }, { name: '2', width: 4 }, { name: '3', width: 5 }, { name: '4', width: 3 }], [3, 3, 3, 3]],
    [[{ name: '1', width: 4 }, { name: '2', width: 3 }, { name: '3', width: 5 }, { name: '4', width: 3 }], [3, 3, 3, 3]],
    [[{ name: '1', width: 3 }, { name: '2', width: 5 }, { name: '3', width: 4 }, { name: '4', width: 3 }], [3, 3, 3, 3]],
    [[{ name: '1', width: 4 }, { name: '2', width: 4 }, { name: '3', width: 4 }, { name: '4', width: 3 }], [3, 3, 3, 3]],
    [[{ name: '1', width: 5 }, { name: '2', width: 3 }, { name: '3', width: 4 }, { name: '4', width: 3 }], [3, 3, 3, 3]],
    [[{ name: '1', width: 3 }, { name: '2', width: 6 }, { name: '3', width: 3 }, { name: '4', width: 3 }], [3, 3, 3, 3]],
    [[{ name: '1', width: 4 }, { name: '2', width: 5 }, { name: '3', width: 3 }, { name: '4', width: 3 }], [3, 3, 3, 3]],
    [[{ name: '1', width: 5 }, { name: '2', width: 4 }, { name: '3', width: 3 }, { name: '4', width: 3 }], [3, 3, 3, 3]],
    [[{ name: '1', width: 6 }, { name: '2', width: 3 }, { name: '3', width: 3 }, { name: '4', width: 3 }], [3, 3, 3, 3]],
    // expanding columns
    [[{ name: '1', width: 10 }], [12]],
    [[{ name: '1', width: 1 }, { name: '2', width: 3 }], [3, 9]],
    [[{ name: '1', width: 3 }, { name: '2', width: 3 }], [3, 9]],
    [[{ name: '1', width: 3 }, { name: '2', width: 3 }, { name: '3', width: 3 }], [3, 3, 6]],
    [[{ name: '1', width: 3 }, { name: '2', width: 3 }, { name: '3', width: 2 }, { name: '4', width: 3 }], [3, 3, 3, 3]],
    [[{ name: '1', width: 3 }, { name: '2', width: 3 }, { name: '3', width: 3 }, { name: '4', width: 3 }], [3, 3, 3, 3]]
  ]

  examples.forEach(([input, expected]) => {
    it(input.map((col) => col.width).toString(), () => {
      expect(adjustColumnWidths(input).map((col) => col.width)).toEqual(expected)
    })
  })
})
