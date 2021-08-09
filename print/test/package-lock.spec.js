import { lockfileVersion } from '../package-lock.json'

describe('The package-lock.json', () => {
  test('uses lockFileVersion 1', () => {
    expect(lockfileVersion).toBe(2)
  })
})
