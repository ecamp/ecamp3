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
        @media print { .nested { color: blue; } }
      `,
      'component.vue?vue&type=pdf-style&lang.css'
    )

    expect(result.code).toContain(
      'component.pdfStyle = {"page":{"fontSize":"12px","lineHeight":"1.5","color":"red"},"page-number":{"fontWeight":"bold"}}'
    )
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
})
