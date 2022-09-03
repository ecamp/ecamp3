import campCollaborationColor from '../campCollaborationColor.js'

describe('campCollaborationColor', () => {
  it.each([
    [{}, 'hsl(0,0%,30%)'],
    [null, 'hsl(0,0%,30%)'],
    [undefined, 'hsl(0,0%,30%)'],
    [{ id: 'fffffff', user: null }, 'hsl(15,100%,30%)'],
    [{ id: 'fffffff', user: () => ({ id: '0000000' }) }, 'hsl(0,100%,30%)'],
    [{ id: 'fffffff', _meta: {} }, 'hsl(15,100%,30%)'],
    [{ _meta: { loading: true } }, 'hsl(0,0%,30%)'],
    [{ id: 'fffffff', user: () => ({ _meta: { loading: true } }) }, 'hsl(0,0%,30%)'],
  ])('maps %p to %p', (input, expected) => {
    expect(campCollaborationColor(input)).toEqual(expected)
  })
})
