import { describe, test, expect } from 'vitest'
import { lockfileVersion, packages } from '../../package-lock.json'

describe('The package-lock.json', () => {
  test('uses lockfileVersion 3', () => {
    expect(lockfileVersion).toBe(3)
  })

  // @react-pdf/font registers pdfkit's standard fonts into the pdfkit that it
  // imports itself. With a second pdfkit in the tree, our renderer builds its
  // document from the other one, whose font registry is then empty, and client
  // print dies on the first font lookup with 'Standard font "Helvetica" is not
  // registered'.
  test('holds a single copy of pdfkit', () => {
    const copies = Object.keys(packages).filter((name) =>
      name.endsWith('node_modules/pdfkit')
    )

    expect(copies).toEqual(['node_modules/pdfkit'])
  })

  // ...and that copy has to be the version @react-pdf/font was built against,
  // since anything else is a combination its maintainer has not adopted. The
  // pin is an exact version, so this compares exactly; should react-pdf ever
  // switch to a range, this fails and we decide deliberately.
  test('pins pdfkit to the version @react-pdf/font declares', () => {
    const declared = packages['node_modules/@react-pdf/font'].dependencies.pdfkit

    expect(packages['node_modules/pdfkit'].version).toBe(declared)
  })
})
