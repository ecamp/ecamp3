import ApiTextField from '../ApiTextField'
import ApiWrapper from '@/components/form/api/ApiWrapper'
import Vue from 'vue'
import Vuetify from 'vuetify'
import flushPromises from 'flush-promises'
import formBaseComponents from '@/plugins/formBaseComponents'
import merge from 'lodash/merge'
import { ApiMock } from '@/components/form/api/__tests__/ApiMock'
import { i18n } from '@/plugins'
import { mount as mountComponent } from '@vue/test-utils'
import { waitForDebounce } from '@/test/util'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('An ApiTextField', () => {
  let vuetify
  let wrapper
  let apiMock

  const fieldName = 'test-field/123'
  const TEXT_1 = 'some text'
  const TEXT_2 = 'another text'

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
      components: { ApiTextField },
      props: {
        fieldName: { type: String, default: fieldName },
      },
      template: `
        <div data-app>
          <api-text-field
            :auto-save="false"
            :fieldname="fieldName"
            uri="test-field/123"
            label="Test field"
            required="true"
          />
        </div>
      `,
    })
    apiMock.get().thenReturn(ApiMock.success(TEXT_1).forFieldName(fieldName))
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
    apiMock.patch().thenReturn(ApiMock.success(TEXT_2))
    wrapper = mount()

    await flushPromises()

    const input = wrapper.find('input')
    await input.setValue(TEXT_2)
    await input.trigger('submit')

    await waitForDebounce()
    await flushPromises()

    expect(apiMock.getMocks().patch).toBeCalledTimes(1)
    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(TEXT_2)
  })

  test('updates state if value in store is refreshed and has new value', async () => {
    wrapper = mount()
    apiMock.get().thenReturn(ApiMock.success(TEXT_2).forFieldName(fieldName))

    wrapper.findComponent(ApiWrapper).vm.reload()

    await waitForDebounce()
    await flushPromises()

    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(TEXT_2)
    expect(wrapper.find('input[type=text]').element.value).toBe(TEXT_2)
  })
})
