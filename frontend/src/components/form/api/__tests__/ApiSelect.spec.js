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

Vue.use(Vuetify)
Vue.use(formBaseComponents)

function mockPromiseResolving (value) {
  return new Promise((resolve, reject) => {
    const timer = setTimeout(() => {
      clearTimeout(timer)
      resolve(value)
    }, 100)
  })
}

describe('An ApiSelect', () => {
  let vuetify
  let wrapper

  const fieldName = 'test-field/123'

  const FIRST_OPTION = {
    value: 1,
    text: 'firstOption'
  }
  const SECOND_OPTION = {
    value: '2',
    text: 'secondOption'
  }

  const selectValues = [
    FIRST_OPTION,
    SECOND_OPTION
  ]

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
        selectValues: { type: Array, default: () => selectValues }
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
      `
    })
    const defaultOptions = {
      mocks: {
        $tc: () => {
        },
        api: {
          get: () => {
            return {
              [fieldName]: FIRST_OPTION.value,
              _meta: {
                load: Promise.resolve(FIRST_OPTION.value)
              }
            }
          }
        }
      }
    }
    return mountComponent(app, { vuetify, i18n, attachTo: document.body, ...merge(defaultOptions, options) })
  }

  const waitForDebounce = () => new Promise((resolve) => setTimeout(resolve, 110))

  test('renders correctly', async () => {
    wrapper = mount()
    await waitForDebounce()
    await flushPromises()

    expect(wrapper).toMatchSnapshot('closed')

    await wrapper.find('.v-input__slot').trigger('click')
    await waitForDebounce()
    await flushPromises()
    expect(wrapper).toMatchSnapshot('open')
  })

  test('triggers api.patch and status update if input changes', async () => {
    const mock = { patch: () => mockPromiseResolving(SECOND_OPTION.value) }
    const patch = jest.spyOn(mock, 'patch')
    wrapper = mount({
      mocks: {
        api: {
          patch
        }
      }
    })
    await flushPromises()

    await wrapper.find('.v-input__slot').trigger('click')
    await wrapper.findAll('[role="option"]').at(1).trigger('click')
    await wrapper.find('input').trigger('submit')

    await waitForDebounce()
    await flushPromises()

    expect(patch).toBeCalledTimes(1)
    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(SECOND_OPTION.value)
  })
})
