import { calculateNextSlotName, limitColumnWidths } from '../calculateNextSlotName.js'

describe('generating a next slot name', () => {
  const examples = [
    [[], '1'],
    [[''], '1'],
    [['0'], '1'],
    [['a'], 'b'],
    [['A'], 'B'],
    [['9'], '19'],
    [['z'], '1z'],
    [['Z'], '1Z'],
    [['0.'], '1.'],
    [['9.'], '19.'],
    [['999'], '1999'],
    [['9zZ'], '19zZ'],
    [['a', '9zZ', ''], '19zZ'],
    [['', 'a', '9zZ'], '19zZ'],
    [['1z', 'z'], '2z'],
    [['1z', '2z'], '3z'],
    [['mitte', 'oben', 'unten'], 'unteo'],
    [['😋'], '1😋'],
    [['a😋'], 'b😋']
  ]

  examples.forEach(([input, expected]) => {
    it(input.toString(), () => {
      expect(calculateNextSlotName(input)).toEqual(expected)
    })
  })
})

describe('adjusting the column widths', () => {
  const examples = [
    [[], []],
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
    [[{ name: '1', width: 6 }, { name: '2', width: 3 }, { name: '3', width: 3 }, { name: '4', width: 3 }], [3, 3, 3, 3]]
  ]

  examples.forEach(([input, expected]) => {
    it(input.map(col => col.width).toString(), () => {
      expect(limitColumnWidths(input).map(col => col.width)).toEqual(expected)
    })
  })
})
