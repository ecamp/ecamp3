// Libraries
import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'

import { mount as mountComponent } from '@vue/test-utils'
import ApiSelect from '../ApiSelect.vue'
import flushPromises from 'flush-promises'
import ApiWrapper from '@/components/form/api/ApiWrapper'
import { i18n } from '@/plugins'
import merge from 'lodash/merge'
import { ApiMock } from '@/components/form/api/__tests__/ApiMock'
import { waitForDebounce } from '@/test/util'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('An ApiSelect', () => {
  let vuetify
  let wrapper
  let apiMock

  const fieldName = 'test-field/123'

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
    vuetify = new Vuetify()
    apiMock = ApiMock.create()
  })

  afterEach(() => {
    jest.restoreAllMocks()
    wrapper.destroy()
  })

  const mount = (options) => {
    const app = Vue.component('App', {
      components: { ApiSelect },
      props: {
        fieldName: { type: String, default: fieldName },
        selectValues: { type: Array, default: () => selectValues },
      },
      template: `
        <div data-app>
        <api-select
          :auto-save="false"
          :fieldname="fieldName"
          uri="test-field/123"
          label="Test field"
          required="true"
          :items="selectValues"
        />
        </div>
      `,
    })
    apiMock.get().thenReturn(ApiMock.success(FIRST_OPTION.value).forFieldName(fieldName))
    const defaultOptions = {
      mocks: {
        $tc: () => {},
        api: apiMock.getMocks(),
      },
    }
    return mountComponent(app, {
      vuetify,
      i18n,
      attachTo: document.body,
      ...merge(defaultOptions, options),
    })
  }

  test('triggers api.patch and status update if input changes', async () => {
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
    apiMock.get().thenReturn(ApiMock.success(SECOND_OPTION.value).forFieldName(fieldName))

    wrapper.findComponent(ApiWrapper).vm.reload()

    await waitForDebounce()
    await flushPromises()

    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(SECOND_OPTION.value)
    expect(wrapper.html()).toContain(SECOND_OPTION.text)
    expect(wrapper.html()).not.toContain(FIRST_OPTION.text)
  })
})
