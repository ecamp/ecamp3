// Libraries
import Vue from 'vue'
import Vuetify from 'vuetify'
// import flushPromises from 'flush-promises'

import { mount } from '@vue/test-utils'
import ApiTextField from '../ApiTextField.vue'

Vue.use(Vuetify)

jest.useFakeTimers()

let url = ''
let data = ''

const mockApi = {
  patch: jest.fn((_url, _data) => {
    return new Promise((resolve, reject) => {
      url = _url
      data = _data
      setTimeout(() => {
        resolve()
      }, 0)
    })
  })
}

describe('ApiTextField.vue', () => {
  let vuetify

  const props = {
    value: 'Test Value',
    fieldname: 'test-field',
    uri: 'test-field/123',
    label: 'Test Field'
  }

  beforeEach(() => {
    vuetify = new Vuetify()
  })

  // keep this the first test --> otherwise elment IDs change constantly
  test('renders correctly', () => {
    const wrapper = mount(ApiTextField, {
      vuetify,
      propsData: props
    })

    expect(wrapper.element).toMatchSnapshot()
  })

  test('input change triggers api.patch call and status update', async () => {
    const wrapper = mount(ApiTextField, {
      vuetify,
      propsData: props,
      mocks: {
        api: mockApi
      }
    })

    const newValue = 'new value'

    // contains 1 v-text-field
    expect(wrapper.find({ name: 'VTextField' }).exists()).toBe(true)

    wrapper.find('input').setValue(newValue)
    expect(mockApi.patch).toBeCalledTimes(1)
    expect(mockApi.patch).toBeCalledWith(props.uri, { [props.fieldname]: newValue })

    await wrapper.vm.$nextTick()
    expect(wrapper.find({ name: 'StatusIcon' }).vm.status).toBe('saving')

    // wait for patch Promise to resolve
    jest.runAllTimers()
    await wrapper.vm.$nextTick()

    expect(wrapper.find({ name: 'StatusIcon' }).vm.status).toBe('success')

    // wait for success icon to vanish
    jest.runAllTimers()
    await wrapper.vm.$nextTick()

    expect(wrapper.find({ name: 'StatusIcon' }).vm.status).toBe('init')
  })
})
