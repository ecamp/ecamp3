import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { flushPromises, shallowMount } from '@vue/test-utils'
import PrintPreview from '../PrintPreview.vue'
import { generatePdf } from '../generatePdf.js'

vi.mock('../generatePdf.js', () => ({ generatePdf: vi.fn() }))
vi.mock('@/plugins/index.js', () => ({ componentI18n: { messages: { value: {} } } }))

describe('PrintPreview', () => {
  let wrapper

  beforeEach(() => {
    vi.useFakeTimers()
    URL.createObjectURL = vi.fn(() => 'blob:preview')
    URL.revokeObjectURL = vi.fn()
  })

  afterEach(() => {
    wrapper?.unmount()
    wrapper = null
    vi.useRealTimers()
    vi.clearAllMocks()
  })

  function mountPreview() {
    wrapper = shallowMount(PrintPreview, {
      props: { config: { contents: [], options: { pageNumbers: false } } },
      global: {
        mocks: {
          $t: (key) => key,
          $store: { state: { lang: { language: 'de' } } },
          api: { get: vi.fn() },
        },
      },
    })
    return wrapper
  }

  it('aborts the previous render and only displays the latest configuration', async () => {
    const pending = []
    generatePdf.mockImplementation((data, onProgress, signal) => {
      return new Promise((resolve) => pending.push({ data, signal, resolve }))
    })
    mountPreview()
    await vi.advanceTimersByTimeAsync(250)
    expect(pending).toHaveLength(1)

    await wrapper.setProps({ config: { contents: [], options: { pageNumbers: true } } })
    expect(pending[0].signal.aborted).toBe(true)
    await vi.advanceTimersByTimeAsync(250)
    expect(pending).toHaveLength(2)
    expect(pending[1].data.config.options.pageNumbers).toBe(true)

    pending[0].resolve({ blob: 'old' })
    await flushPromises()
    expect(URL.createObjectURL).not.toHaveBeenCalled()
    pending[1].resolve({ blob: 'new' })
    await flushPromises()
    expect(URL.createObjectURL).toHaveBeenCalledWith('new')
    expect(wrapper.find('iframe').attributes('src')).toContain('blob:preview')
  })

  it('cancels work and revokes the PDF when the preview is closed', async () => {
    let signal
    generatePdf.mockImplementation((data, onProgress, abortSignal) => {
      signal = abortSignal
      return new Promise(() => {})
    })
    mountPreview()
    await vi.advanceTimersByTimeAsync(250)
    wrapper.unmount()
    wrapper = null
    expect(signal.aborted).toBe(true)
  })

  it('revokes a displayed PDF on unmount', async () => {
    generatePdf.mockResolvedValue({ blob: 'pdf' })
    mountPreview()
    await vi.advanceTimersByTimeAsync(250)
    await flushPromises()
    wrapper.unmount()
    wrapper = null
    expect(URL.revokeObjectURL).toHaveBeenCalledWith('blob:preview')
  })
})
