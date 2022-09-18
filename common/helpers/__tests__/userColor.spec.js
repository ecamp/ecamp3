import userColor from '../userColor.js'

describe('userColor', () => {
  it.each([
    [{ id: 'fffffff' }, 'hsl(15,100%,30%)'],
    [{ name: 'test' }, 'hsl(0,0%,30%)'],
    [{ _meta: {} }, 'hsl(0,0%,30%)'],
    [{ _meta: { loading: true } }, 'hsl(0,0%,30%)'],
  ])('maps %p to %p', (input, expected) => {
    expect(userColor(input)).toEqual(expected)
  })
})
