import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import ToastHost from '@/components/toast/ToastHost.vue'
import ToastNotification from '@/components/toast/ToastNotification.vue'
import { clearToasts, useToast } from '@/components/toast/useToast.js'

setupVuetify()

describe('ToastHost', () => {
  beforeEach(() => {
    vi.useFakeTimers()
    clearToasts()
  })

  afterEach(() => {
    clearToasts()
    vi.useRealTimers()
  })

  it('renders two notifications and displays the next queued notification', async () => {
    const wrapper = mount(ToastHost)
    const toast = useToast()

    toast.info('First')
    toast.info('Second')
    toast.error('Third')
    await wrapper.vm.$nextTick()

    const visibleNotifications = wrapper.findAllComponents(ToastNotification)
    expect(visibleNotifications).toHaveLength(2)
    expect(visibleNotifications.map((item) => item.text())).toEqual(['Second', 'First'])

    visibleNotifications[1].vm.$emit('dismiss', visibleNotifications[1].props('toast').id)
    await wrapper.vm.$nextTick()

    expect(
      wrapper.findAllComponents(ToastNotification).map((item) => item.text())
    ).toEqual(['Third', 'Second'])
  })
})
