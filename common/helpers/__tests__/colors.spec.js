import { campCollaborationHslColor, idToHslColor, userHslColor } from '../colors.js'

describe('idToHslColor', () => {
  it.each([
    [
      [undefined, false],
      [0, 0, 0.3],
    ],
    [
      [null, false],
      [0, 0, 0.3],
    ],
    [
      ['', false],
      [0, 0, 0.3],
    ],
    [
      ['0000000', false],
      [0, 1, 0.3],
    ],
    [
      ['0000000', true],
      [0, 0, 0.3],
    ],
    [
      ['fffffff', false],
      [15, 1, 0.3],
    ],
    [
      ['Wrong input', false],
      [0, 1, 0.3],
    ],
  ])('maps %p to %p', (input, expected) => {
    expect(idToHslColor(...input)).toEqual(expected)
  })

  it('uses false as default for inactive', () => {
    expect(idToHslColor('fffffff')).toEqual([15, 1, 0.3])
  })
})

describe('userHslColor', () => {
  it.each([
    [{ id: 'fffffff' }, [15, 1, 0.3]],
    [{ name: 'test' }, [0, 0, 0.3]],
    [{ _meta: {} }, [0, 0, 0.3]],
    [{ _meta: { loading: true } }, [0, 0, 0.3]],
  ])('maps %p to %p', (input, expected) => {
    expect(userHslColor(input)).toEqual(expected)
  })
})

describe('campCollaborationHslColor', () => {
  it.each([
    [{}, [0, 0, 0.3]],
    [null, [0, 0, 0.3]],
    [undefined, [0, 0, 0.3]],
    [{ id: 'fffffff', user: null }, [15, 1, 0.3]],
    [{ id: 'fffffff', user: () => ({ id: '0000000' }) }, [0, 1, 0.3]],
    [{ id: 'fffffff', _meta: {} }, [15, 1, 0.3]],
    [{ _meta: { loading: true } }, [0, 0, 0.3]],
    [{ id: 'fffffff', user: () => ({ _meta: { loading: true } }) }, [0, 0, 0.3]],
  ])('maps %p to %p', (input, expected) => {
    expect(campCollaborationHslColor(input)).toEqual(expected)
  })
})
