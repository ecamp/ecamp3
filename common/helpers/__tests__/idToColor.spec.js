import idToColor from '../idToColor.js'

describe('idToColor', () => {
  it.each([
    [[undefined, false], 'hsl(0,0%,30%)'],
    [[null, false], 'hsl(0,0%,30%)'],
    [['', false], 'hsl(0,0%,30%)'],
    [['0000000', false], 'hsl(0,100%,30%)'],
    [['0000000', true], 'hsl(0,0%,30%)'],
    [['fffffff', false], 'hsl(15,100%,30%)'],
    [['Wrong input', false], 'hsl(0,100%,30%)'],
  ])('maps %p to %p', (input, expected) => {
    expect(idToColor(...input)).toEqual(expected)
  })

  it('uses false as default for inactive', () => {
    expect(idToColor('fffffff')).toEqual('hsl(15,100%,30%)')
  })
})
