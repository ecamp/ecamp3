import { describe, expect, test, vi } from 'vitest'
import { vuePdfStylePlugin } from '../vue-pdf-style-plugin.js'

describe('vue pdf style plugin', () => {
  test('transforms supported pdf style rules', () => {
    const result = vuePdfStylePlugin.transform(
      `
        /* ignored */
        .page { font-size: 12px; line-height: 1.5; }
        .page { color: red; }
        .page-number { font-weight: bold; }
      `,
      'component.vue?vue&type=pdf-style&lang.css'
    )

    expect(result.code).toContain(
      'component.pdfStyle = {"page":{"fontSize":"12px","lineHeight":"1.5","color":"red"},"page-number":{"fontWeight":"bold"}}'
    )
  })

  test('ignores comments inside supported rules', () => {
    const result = vuePdfStylePlugin.transform(
      '.page { /* ignored */ color: red; }',
      'component.vue?vue&type=pdf-style&lang.css'
    )

    expect(result.code).toContain('component.pdfStyle = {"page":{"color":"red"}}')
  })

  test('rejects unsupported at-rules', () => {
    expect(() =>
      vuePdfStylePlugin.transform(
        '@media print { .nested { color: blue; } }',
        'component.vue?vue&type=pdf-style&lang.css'
      )
    ).toThrow('Unsupported CSS node "atrule" in pdf-style')
  })

  test('rejects nested rules', () => {
    expect(() =>
      vuePdfStylePlugin.transform(
        '.page { color: red; .nested { color: blue; } }',
        'component.vue?vue&type=pdf-style&lang.css'
      )
    ).toThrow('Unsupported CSS node "rule" in pdf-style')
  })

  test('rejects at-rules nested in rules', () => {
    expect(() =>
      vuePdfStylePlugin.transform(
        '.page { color: red; @media print { color: blue; } }',
        'component.vue?vue&type=pdf-style&lang.css'
      )
    ).toThrow('Unsupported CSS node "atrule" in pdf-style')
  })

  test('logs and skips complex selectors', () => {
    const error = vi.spyOn(console, 'error').mockImplementation(() => {})

    const result = vuePdfStylePlugin.transform(
      '.valid { color: green; } .complex .selector { color: red; }',
      'component.vue?vue&type=pdf-style&lang.css'
    )

    expect(result.code).toContain('component.pdfStyle = {"valid":{"color":"green"}}')
    expect(error).toHaveBeenCalledWith(
      'Only simple single-class selectors are supported in pdf-style. Got the selector',
      '.complex .selector'
    )
    error.mockRestore()
  })

  describe('font-feature-settings', () => {
    const fontFeatureSettings = (value) => {
      const { code } = vuePdfStylePlugin.transform(
        `.a { font-feature-settings: ${value}; }`,
        'component.vue?vue&type=pdf-style&lang.css'
      )
      return JSON.parse(code.match(/component\.pdfStyle = (.*);/s)[1]).a
        .fontFeatureSettings
    }

    test('converts feature tags to the format expected by react-pdf', () => {
      expect(fontFeatureSettings(`'tnum', "lnum"`)).toEqual({ tnum: 1, lnum: 1 })
    })

    test('supports switching font features on and off', () => {
      expect(fontFeatureSettings("'liga' off, 'tnum' on, 'kern' 0, 'lnum' 1")).toEqual({
        liga: 0,
        tnum: 1,
        kern: 0,
        lnum: 1,
      })
    })

    test('supports numeric feature values', () => {
      expect(fontFeatureSettings("'swsh' 2")).toEqual({ swsh: 2 })
    })

    test('applies no font features for the normal keyword', () => {
      expect(fontFeatureSettings('normal')).toEqual({})
    })
  })
})
