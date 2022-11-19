import factory from '../packageDirectory.js'
import path from 'path'
import fs from 'fs'

const { packageDirectory } = factory(path, fs)

describe('packageDirectory', () => {
  it('resolves the location of the closest package.json', () => {
    const packageJsonFile = path.resolve(__dirname, '../../package.json')
    fs.writeFileSync(packageJsonFile, '{}')
    expect(packageDirectory(__dirname)).toEqual(path.resolve(__dirname, '../..'))
    fs.unlinkSync(packageJsonFile)
  })
})
