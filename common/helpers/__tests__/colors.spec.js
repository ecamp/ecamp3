import { campCollaborationColor, idToColor, userColor } from '../colors.js'

describe('idToColor', () => {
  it.each([
    [
      [undefined, false],
      [0, 0, 30],
    ],
    [
      [null, false],
      [0, 0, 30],
    ],
    [
      ['', false],
      [0, 0, 30],
    ],
    [
      ['0000000', false],
      [0, 100, 30],
    ],
    [
      ['0000000', true],
      [0, 0, 30],
    ],
    [
      ['fffffff', false],
      [15, 100, 30],
    ],
    [
      ['Wrong input', false],
      [0, 100, 30],
    ],
  ])('maps %p to %p', (input, expected) => {
    expect(idToColor(...input)).toEqual(expected)
  })

  it('uses false as default for inactive', () => {
    expect(idToColor('fffffff')).toEqual([15, 100, 30])
  })
})

describe('userColor', () => {
  it.each([
    [{ id: 'fffffff' }, [15, 100, 30]],
    [{ name: 'test' }, [0, 0, 30]],
    [{ _meta: {} }, [0, 0, 30]],
    [{ _meta: { loading: true } }, [0, 0, 30]],
  ])('maps %p to %p', (input, expected) => {
    expect(userColor(input)).toEqual(expected)
  })
})

describe('campCollaborationColor', () => {
  it.each([
    [{}, [0, 0, 30]],
    [null, [0, 0, 30]],
    [undefined, [0, 0, 30]],
    [{ id: 'fffffff', user: null }, [15, 100, 30]],
    [{ id: 'fffffff', user: () => ({ id: '0000000' }) }, [0, 100, 30]],
    [{ id: 'fffffff', _meta: {} }, [15, 100, 30]],
    [{ _meta: { loading: true } }, [0, 0, 30]],
    [{ id: 'fffffff', user: () => ({ _meta: { loading: true } }) }, [0, 0, 30]],
  ])('maps %p to %p', (input, expected) => {
    expect(campCollaborationColor(input)).toEqual(expected)
  })
})
