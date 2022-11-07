import { campCollaborationColor, idToColor, userColor } from '../colors.js'

describe('idToColor', () => {
  it.each([
    [[undefined, false], '#4d4d4d'],
    [[null, false], '#4d4d4d'],
    [['', false], '#4d4d4d'],
    [['0000000', false], '#900'],
    [['0000000', true], '#4d4d4d'],
    [['fffffff', false], '#992600'],
    [['Wrong input', false], '#900'],
  ])('maps %p to %p', (input, expected) => {
    expect(idToColor(...input)).toEqual(expected)
  })

  it('uses false as default for inactive', () => {
    expect(idToColor('fffffff')).toEqual('#992600')
  })
})

describe('userColor', () => {
  it.each([
    [{ id: 'fffffff' }, '#992600'],
    [{ name: 'test' }, '#4d4d4d'],
    [{ _meta: {} }, '#4d4d4d'],
    [{ _meta: { loading: true } }, '#4d4d4d'],
  ])('maps %p to %p', (input, expected) => {
    expect(userColor(input)).toEqual(expected)
  })
})

describe('campCollaborationColor', () => {
  it.each([
    [{}, '#4d4d4d'],
    [null, '#4d4d4d'],
    [undefined, '#4d4d4d'],
    [{ id: 'fffffff', user: null }, '#992600'],
    [{ id: 'fffffff', user: () => ({ id: '0000000' }) }, '#900'],
    [{ id: 'fffffff', _meta: {} }, '#992600'],
    [{ _meta: { loading: true } }, '#4d4d4d'],
    [{ id: 'fffffff', user: () => ({ _meta: { loading: true } }) }, '#4d4d4d'],
  ])('maps %p to %p', (input, expected) => {
    expect(campCollaborationColor(input)).toEqual(expected)
  })
})
