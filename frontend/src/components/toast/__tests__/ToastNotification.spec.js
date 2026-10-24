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
    expect(wrapper.getComponent({ name: 'VProgressCircular' }).props('modelValue')).toBe(
      40
    )

    await wrapper.trigger('mouseenter')
    await vi.advanceTimersByTimeAsync(10000)
    expect(wrapper.emitted('dismiss')).toBeUndefined()
    expect(wrapper.getComponent({ name: 'VProgressCircular' }).props('modelValue')).toBe(
      40
    )

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

  it('cancels its timeout on click and stays open until explicitly closed', async () => {
    vi.useFakeTimers()
    const wrapper = mount(ToastNotification, {
      props: { toast: createToast() },
    })

    await wrapper.get('.v-alert__content').trigger('click')
    expect(wrapper.findComponent({ name: 'VProgressCircular' }).exists()).toBe(false)

    await wrapper.trigger('mouseleave')
    await vi.advanceTimersByTimeAsync(30000)
    expect(wrapper.emitted('dismiss')).toBeUndefined()

    await wrapper.trigger('mouseenter')
    await wrapper.trigger('mouseleave')
    await vi.advanceTimersByTimeAsync(30000)
    expect(wrapper.emitted('dismiss')).toBeUndefined()

    await wrapper.get('.v-alert__close button').trigger('click')
    expect(wrapper.emitted('dismiss')).toEqual([[1]])
  })

  it('offers no actions unless it has some', () => {
    const wrapper = mount(ToastNotification, {
      props: { toast: createToast() },
    })

    expect(wrapper.find('[data-testid="toast-action"]').exists()).toBe(false)
  })

  it('runs the action used and dismisses itself', async () => {
    vi.useFakeTimers()
    const undo = vi.fn()
    const showAll = vi.fn()
    const wrapper = mount(ToastNotification, {
      props: {
        toast: createToast({
          actions: [
            { label: 'Undo', onClick: undo },
            { label: 'Show all', onClick: showAll },
          ],
        }),
      },
    })

    const actions = wrapper.findAll('[data-testid="toast-action"]')
    expect(actions.map((action) => action.text())).toEqual(['Undo', 'Show all'])
    await actions[1].trigger('click')

    expect(showAll).toHaveBeenCalledOnce()
    expect(undo).not.toHaveBeenCalled()
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
