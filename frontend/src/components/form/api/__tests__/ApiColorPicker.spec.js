import ApiColorPicker from '../ApiColorPicker'
import ApiWrapper from '@/components/form/api/ApiWrapper'
import Vue from 'vue'
import Vuetify from 'vuetify'
import flushPromises from 'flush-promises'
import formBaseComponents from '@/plugins/formBaseComponents'
import merge from 'lodash/merge'
import { ApiMock } from '@/components/form/api/__tests__/ApiMock'
import { extend } from 'vee-validate'
import { i18n } from '@/plugins'
import { mount as mountComponent } from '@vue/test-utils'
import { regex } from 'vee-validate/dist/rules'
import { waitForDebounce } from '@/test/util'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

extend('regex', regex)

describe('An ApiColorPicker', () => {
  let vuetify
  let wrapper
  let apiMock

  const fieldName = 'test-field/123'
  const COLOR_1 = '#ff0000'
  const COLOR_2 = '#ff00ff'

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
      components: { ApiColorPicker },
      props: {
        fieldName: { type: String, default: fieldName },
      },
      template: `
        <div data-app>
          <api-color-picker
            :auto-save="false"
            :fieldname="fieldName"
            uri="test-field/123"
            label="Test field"
            required="true"
          />
        </div>
      `,
    })
    apiMock.get().thenReturn(ApiMock.success(COLOR_1).forFieldName(fieldName))
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
    apiMock.patch().thenReturn(ApiMock.success(COLOR_2))
    wrapper = mount()

    await flushPromises()

    const input = wrapper.find('input')
    await input.setValue(COLOR_2)
    await input.trigger('submit')

    await waitForDebounce()
    await flushPromises()

    expect(apiMock.getMocks().patch).toBeCalledTimes(1)
    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(COLOR_2)
  })

  test('updates state if value in store is refreshed and has new value', async () => {
    wrapper = mount()
    apiMock.get().thenReturn(ApiMock.success(COLOR_2).forFieldName(fieldName))

    wrapper.findComponent(ApiWrapper).vm.reload()

    await waitForDebounce()
    await flushPromises()

    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(COLOR_2)
    expect(wrapper.find('input[type=text]').element.value).toBe(COLOR_2)
  })
})
