import { lockfileVersion } from '../../package-lock.json'

describe('The package-lock.json', async () => {
  test('uses lockFileVersion 1', async () => {
    expect(lockfileVersion).toBe(1)
  })
})
