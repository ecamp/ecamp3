import { describe, expect, test, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import ApiWrapperAppend from '../ApiWrapperAppend.vue'
import { setupVuetify } from '/tests/setupVuetify.js'

setupVuetify()

describe('An ApiWrapperAppend', () => {
  // ApiWrapperAppend is rendered inside the append-inner slot of the wrapped
  // field (e.g. ESelect's v-select). Vuetify's fields (VTextField's
  // onControlMousedown, forwarded by e.g. VSelect's onMousedown:control)
  // open their menu/picker on `mousedown` of the field, not on `click`. So
  // a mousedown on one of our buttons must not bubble up to the field, or
  // it would toggle the menu open on top of performing the button's own
  // action. We mount the component inside a parent that reacts to
  // mousedown the same way the wrapped field would, and assert it never
  // fires - while the button's own click action still does.
  // See https://github.com/ecamp/ecamp3/issues/3762
  const mountInsideField = (wrapperProps) => {
    const onFieldMousedown = vi.fn()
    const app = {
      components: { ApiWrapperAppend },
      data: () => ({ wrapperProps }),
      methods: { onFieldMousedown },
      template: `
        <div @mousedown="onFieldMousedown">
          <api-wrapper-append :wrapper="wrapperProps" />
        </div>
      `,
    }
    const wrapper = mount(app, {
      attachTo: document.body,
      global: { mocks: { $t: (key) => key } },
    })
    return { wrapper, onFieldMousedown }
  }

  const click = async (element) => {
    // a real click is preceded by mousedown+mouseup; jsdom's `trigger`
    // dispatches only the event you ask for, so mimic that sequence
    await element.trigger('mousedown')
    await element.trigger('mouseup')
    await element.trigger('click')
  }

  test('clicking the retry button after a failed save does not bubble to the field', async () => {
    const onSave = vi.fn()
    const { wrapper, onFieldMousedown } = mountInsideField({
      hasServerError: true,
      autoSave: true,
      dirty: false,
      hasLoadingError: false,
      status: 'init',
      on: { save: onSave, reset: vi.fn(), reload: vi.fn() },
    })

    await click(wrapper.find('[aria-label="global.button.tryagain"]'))

    expect(onSave).toHaveBeenCalledTimes(1)
    expect(onFieldMousedown).not.toHaveBeenCalled()
  })

  test('clicking the cancel button after a failed save does not bubble to the field', async () => {
    const onReset = vi.fn()
    const { wrapper, onFieldMousedown } = mountInsideField({
      hasServerError: true,
      autoSave: true,
      dirty: false,
      hasLoadingError: false,
      status: 'init',
      on: { save: vi.fn(), reset: onReset, reload: vi.fn() },
    })

    await click(wrapper.find('[aria-label="global.button.cancel"]'))

    expect(onReset).toHaveBeenCalledTimes(1)
    expect(onFieldMousedown).not.toHaveBeenCalled()
  })

  test('clicking the reload button after a failed load does not bubble to the field', async () => {
    const onReload = vi.fn()
    const { wrapper, onFieldMousedown } = mountInsideField({
      hasServerError: false,
      autoSave: true,
      dirty: false,
      hasLoadingError: true,
      status: 'init',
      on: { save: vi.fn(), reset: vi.fn(), reload: onReload },
    })

    await click(wrapper.find('button'))

    expect(onReload).toHaveBeenCalledTimes(1)
    expect(onFieldMousedown).not.toHaveBeenCalled()
  })
})
