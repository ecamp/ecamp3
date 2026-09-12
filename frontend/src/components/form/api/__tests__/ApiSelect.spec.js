import { afterEach, beforeEach, describe, expect, test, vi } from 'vitest'
import ApiSelect from '../ApiSelect.vue'
import flushPromises from 'flush-promises'
import ApiWrapper from '@/components/form/api/ApiWrapper.vue'
import { i18n } from '@/plugins'
import { merge } from 'lodash-es'
import { ApiMock } from '@/components/form/api/__tests__/ApiMock'
import { mount as mountComponent } from '@vue/test-utils'
import { waitForDebounce } from '@/test/util'
import { setupVuetify } from '/tests/setupVuetify.js'

setupVuetify()

describe('An ApiSelect', () => {
  let wrapper
  let apiMock

  const path = 'test-field/123'

  const FIRST_OPTION = {
    value: 1,
    text: 'firstOption',
  }
  const SECOND_OPTION = {
    value: '2',
    text: 'secondOption',
  }

  const selectValues = [FIRST_OPTION, SECOND_OPTION]

  beforeEach(() => {
    apiMock = ApiMock.create()
    // default GET stub; tests can override it before calling mount()
    apiMock.get().thenReturn(ApiMock.success(FIRST_OPTION.value).forPath(path))
  })

  afterEach(() => {
    vi.restoreAllMocks()
    wrapper?.unmount()
  })

  const mount = (options, autoSave = false) => {
    const app = {
      components: { ApiSelect },
      props: {
        path: { type: String, default: path },
        selectValues: { type: Array, default: () => selectValues },
      },
      template: `
        <div data-app>
          <api-select
            :auto-save="${autoSave}"
            :path="path"
            uri="test-field/123"
            label="Test field"
            required="true"
            :items="selectValues"
          />
        </div>
      `,
    }
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

  test.skip('triggers api.patch and status update if input changes', async () => {
    apiMock.patch().thenReturn(ApiMock.success(SECOND_OPTION.value))
    wrapper = mount()

    await flushPromises()

    await wrapper.find('.v-input__slot').trigger('click')
    await wrapper.findAll('[role="option"]').at(1).trigger('click')
    await wrapper.find('input').trigger('submit')

    await waitForDebounce()
    await flushPromises()

    expect(apiMock.getMocks().patch).toBeCalledTimes(1)
    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(SECOND_OPTION.value)
  })

  test('updates state if value in store is refreshed and has new value', async () => {
    wrapper = mount()
    apiMock.get().thenReturn(ApiMock.success(SECOND_OPTION.value).forPath(path))

    wrapper.findComponent(ApiWrapper).vm.reload()

    await waitForDebounce()
    await flushPromises()

    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(SECOND_OPTION.value)
    expect(wrapper.html()).toContain(SECOND_OPTION.text)
    expect(wrapper.html()).not.toContain(FIRST_OPTION.text)
  })

  const click = async (element) => {
    await element.trigger('mousedown')
    await element.trigger('mouseup')
    await element.trigger('click')
  }

  const dropdownIsOpen = () =>
    wrapper.get('.v-select').classes().includes('v-select--active-menu')

  test('clicking the reload button after a failed load does not open the dropdown', async () => {
    apiMock.get().thenReturn(ApiMock.networkError().forPath(path))
    wrapper = mount()
    await flushPromises()

    expect(wrapper.findComponent(ApiWrapper).vm.hasLoadingError).toBe(true)
    apiMock.get().thenReturn(ApiMock.success(FIRST_OPTION.value).forPath(path))

    await click(wrapper.get('button'))

    expect(dropdownIsOpen()).toBe(false)
    expect(wrapper.findComponent(ApiWrapper).vm.hasLoadingError).toBe(false)
  })

  test('clicking the retry button after a failed save does not open the dropdown', async () => {
    wrapper = mount(undefined, true)
    await flushPromises()

    const apiWrapper = wrapper.findComponent(ApiWrapper)
    apiMock
      .getMocks()
      .patch.mockImplementation(() =>
        Promise.reject({ message: 'A network error occurred.' })
      )
    apiWrapper.vm.onInput(SECOND_OPTION.value)

    await waitForDebounce()
    await flushPromises()

    expect(apiWrapper.vm.hasServerError).toBe(true)
    apiMock
      .getMocks()
      .patch.mockImplementation(() => Promise.resolve(SECOND_OPTION.value))

    await click(wrapper.get('[aria-label="global.button.tryagain"]'))

    expect(dropdownIsOpen()).toBe(false)
  })

  test('clicking the cancel button after a failed save does not open the dropdown', async () => {
    wrapper = mount(undefined, true)
    await flushPromises()

    const apiWrapper = wrapper.findComponent(ApiWrapper)
    apiMock
      .getMocks()
      .patch.mockImplementation(() =>
        Promise.reject({ message: 'A network error occurred.' })
      )
    apiWrapper.vm.onInput(SECOND_OPTION.value)

    await waitForDebounce()
    await flushPromises()

    expect(apiWrapper.vm.hasServerError).toBe(true)

    await click(wrapper.get('[aria-label="global.button.cancel"]'))

    expect(dropdownIsOpen()).toBe(false)
    expect(apiWrapper.vm.hasServerError).toBe(false)
  })
})
