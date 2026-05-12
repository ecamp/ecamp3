import { afterEach, beforeEach, describe, expect, test, vi } from 'vitest'
import ApiCheckbox from '../ApiCheckbox.vue'
import ApiWrapper from '@/components/form/api/ApiWrapper.vue'
import flushPromises from 'flush-promises'
import { merge } from 'lodash-es'
import { ApiMock } from '@/components/form/api/__tests__/ApiMock'
import { i18n } from '@/plugins'
import { mount as mountComponent } from '@vue/test-utils'
import { waitForDebounce } from '@/test/util'
import { setupVuetify } from '/tests/setupVuetify.js'

setupVuetify()

describe('An ApiCheckbox', () => {
  let wrapper
  let apiMock

  const path = 'test-field/123'

  beforeEach(() => {
    apiMock = ApiMock.create()
  })

  afterEach(() => {
    vi.restoreAllMocks()
    wrapper?.unmount()
  })

  const mount = (options) => {
    const app = {
      components: { ApiCheckbox },
      props: {
        path: { type: String, default: path },
      },
      template: `
        <div data-app>
          <api-checkbox
            :auto-save="false"
            :path="path"
            uri="test-field/123"
            label="Test field"
            required="true"
          />
        </div>
      `,
    }
    apiMock.get().thenReturn(ApiMock.success(true).forPath(path))
    const defaultOptions = {
      global: {
        mocks: {
          $t: (key) => key,
          api: apiMock.getMocks(),
        },
      },
    }
    return mountComponent(app, {
      i18n,
      attachTo: document.body,
      ...merge(defaultOptions, options),
    })
  }

  test('triggers api.patch and status update if input changes', async () => {
    apiMock.patch().thenReturn(ApiMock.success(false))
    wrapper = mount()

    await flushPromises()

    const input = wrapper.find('input')
    await input.trigger('click')
    await input.trigger('submit')

    await waitForDebounce()
    await flushPromises()

    expect(apiMock.getMocks().patch).toBeCalledTimes(1)
    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(false)
  })

  test('updates state if value in store is refreshed and has new value', async () => {
    wrapper = mount()
    apiMock.get().thenReturn(ApiMock.success(false).forPath(path))

    wrapper.findComponent(ApiWrapper).vm.reload()

    await waitForDebounce()
    await flushPromises()

    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(false)
    // aria-checked is null when value is falsy (unchecked)
    expect(
      wrapper.find('input[type=checkbox]').element.getAttribute('aria-checked')
    ).toBeFalsy()
  })
})
