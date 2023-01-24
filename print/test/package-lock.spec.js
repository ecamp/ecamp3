import { lockfileVersion } from '../package-lock.json'

describe('The package-lock.json', () => {
  test('uses lockfileVersion 3', () => {
    expect(lockfileVersion).toBe(3)
  })
})
