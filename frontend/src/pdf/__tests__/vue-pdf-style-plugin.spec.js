import { expect, it, describe } from 'vitest'
import { vuePdfStylePlugin } from '../vue-pdf-style-plugin.js'

const transform = (css) => {
  const { code } = vuePdfStylePlugin.transform(css, 'Test.vue?vue&type=pdf-style')
  return JSON.parse(code.match(/component\.pdfStyle = (.*);/s)[1])
}

const fontFeatureSettings = (value) =>
  transform(`.a { font-feature-settings: ${value}; }`).a.fontFeatureSettings

describe('vuePdfStylePlugin', () => {
  it('keeps declarations other than font-feature-settings as they are', () => {
    expect(transform('.a { margin-bottom: 12pt; }')).toEqual({
      a: { marginBottom: '12pt' },
    })
  })

  it('converts font-feature-settings to the feature tags expected by react-pdf', () => {
    expect(fontFeatureSettings("'tnum', 'lnum'")).toEqual({ tnum: true, lnum: true })
  })

  it('supports switching font features on and off', () => {
    expect(fontFeatureSettings("'liga' off, 'tnum' on, 'kern' 0, 'lnum' 1")).toEqual({
      liga: false,
      tnum: true,
      kern: false,
      lnum: true,
    })
  })

  it('applies no font features for the normal keyword', () => {
    expect(fontFeatureSettings('normal')).toEqual({})
  })
})
