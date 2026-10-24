import { afterEach, describe, expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import ToastNotification from '@/components/toast/ToastNotification.vue'

setupVuetify()

function createToast(overrides = {}) {
  return {
    id: 1,
    type: 'info',
    content: 'Saved',
    timeout: 30000,
    ...overrides,
  }
}

describe('ToastNotification', () => {
  afterEach(() => {
    vi.useRealTimers()
  })

  it('dismisses after its timeout and pauses while hovered', async () => {
    vi.useFakeTimers()
    const wrapper = mount(ToastNotification, {
      props: { toast: createToast() },
    })

    await vi.advanceTimersByTimeAsync(12000)
    expect(wrapper.getComponent({ name: 'VProgressLinear' }).props('modelValue')).toBe(60)

    await wrapper.trigger('mouseenter')
    await vi.advanceTimersByTimeAsync(10000)
    expect(wrapper.emitted('dismiss')).toBeUndefined()
    expect(wrapper.getComponent({ name: 'VProgressLinear' }).props('modelValue')).toBe(60)

    await wrapper.trigger('mouseleave')
    await vi.advanceTimersByTimeAsync(17999)
    expect(wrapper.emitted('dismiss')).toBeUndefined()

    await vi.advanceTimersByTimeAsync(1)
    expect(wrapper.emitted('dismiss')).toEqual([[1]])
  })

  it('dismisses when its close control is used', () => {
    vi.useFakeTimers()
    const wrapper = mount(ToastNotification, {
      props: { toast: createToast() },
    })

    wrapper.getComponent({ name: 'VAlert' }).vm.$emit('click:close')

    expect(wrapper.emitted('dismiss')).toEqual([[1]])
  })

  it('renders at most three multiline error details and an ellipsis', () => {
    vi.useFakeTimers()
    const wrapper = mount(ToastNotification, {
      props: {
        toast: createToast({
          type: 'error',
          content: {
            lines: ['First', 'Second', 'Third', 'Fourth'],
            generalErrorText: 'General error',
          },
        }),
      },
    })

    expect(wrapper.text()).toContain('First')
    expect(wrapper.text()).toContain('Second')
    expect(wrapper.text()).toContain('Third')
    expect(wrapper.text()).not.toContain('Fourth')
    expect(wrapper.text()).toContain('...')
  })

  it('renders the general error when no details are available', () => {
    vi.useFakeTimers()
    const wrapper = mount(ToastNotification, {
      props: {
        toast: createToast({
          type: 'error',
          content: {
            lines: [],
            generalErrorText: 'General error',
          },
        }),
      },
    })

    expect(wrapper.text()).toContain('General error')
  })
})
