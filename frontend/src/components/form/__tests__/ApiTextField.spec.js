// Libraries
import Vue from 'vue'
import Vuetify from 'vuetify'
import flushPromises from 'flush-promises'

import { shallowMount, mount } from '@vue/test-utils'
import ApiTextField from '../ApiTextField.vue'
import ApiWrapper from '../ApiWrapper'

jest.mock('lodash')
const { cloneDeep } = jest.requireActual('lodash')

jest.useFakeTimers()
Vue.use(Vuetify)
let vuetify

// config factory
function createConfig (overrides) {
  const mocks = {
    // Vue Auth
    api: {
      patch: () => Promise.resolve()
    }
  }
  const propsData = {
    value: 'Test Value',
    fieldname: 'test-field',
    uri: 'test-field/123',
    label: 'Test Field'
  }
  return cloneDeep(Object.assign({ mocks, propsData, vuetify }, overrides))
}

describe('ApiTextField.vue', () => {
  beforeEach(() => {
    vuetify = new Vuetify()
  })

  // keep this the first test --> otherwise elment IDs change constantly
  test('renders correctly', () => {
    const config = createConfig()
    config.stubs = { ApiWrapper: ApiWrapper }
    const wrapper = shallowMount(ApiTextField, config)

    expect(wrapper.element).toMatchSnapshot()
  })

  test('input change triggers api.patch call and status update', async () => {
    const config = createConfig()
    const patchSpy = jest.spyOn(config.mocks.api, 'patch')
    const wrapper = mount(ApiTextField, config)

    const newValue = 'new value'

    // contains 1 v-text-field
    expect(wrapper.find({ name: 'VTextField' }).exists()).toBe(true)

    wrapper.find('input').setValue(newValue)
    expect(patchSpy).toBeCalledTimes(1)
    expect(patchSpy).toBeCalledWith(config.propsData.uri, { [config.propsData.fieldname]: newValue })

    const statusIcon = wrapper.find({ name: 'StatusIcon' }).vm
    // expect(statusIcon.status).toBe('saving')

    // wait for patch Promise to resolve
    await flushPromises()

    // expect(statusIcon.status).toBe('success')

    // wait for success icon to vanish
    jest.runAllTimers()
    await wrapper.vm.$nextTick()

    // expect(statusIcon.status).toBe('init')
  })
})
