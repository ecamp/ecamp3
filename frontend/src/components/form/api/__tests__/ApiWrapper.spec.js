// Libraries
import Vue from 'vue'
import Vuetify from 'vuetify'
import flushPromises from 'flush-promises'
import { shallowMount } from '@vue/test-utils'
import ApiWrapper from '../ApiWrapper.vue'

jest.mock('lodash')
const { cloneDeep } = jest.requireActual('lodash')

jest.useFakeTimers()

Vue.use(Vuetify)
let vuetify

// config factory
function createConfig (overrides) {
  const mocks = {
    api: {
      patch: () => Promise.resolve(),
      get: () => {
        return {
          _meta: {
            load: Promise.resolve()
          }
        }
      }
    }
  }
  const propsData = {
    value: 'Test Value',
    fieldname: 'test-field',
    uri: 'test-field/123'
  }
  return cloneDeep(Object.assign({ mocks, propsData, vuetify }, overrides))
}

describe('Testing autoSave mode', () => {
  beforeEach(() => {
    vuetify = new Vuetify()
  })

  test('init correctly with default values', () => {
    const config = createConfig()
    const wrapper = shallowMount(ApiWrapper, config)
    const vm = wrapper.vm

    expect(vm.value).toBe(config.propsData.value)
    expect(vm.dirty).toBe(false)
    expect(vm.isSaving).toBe(false)
    expect(vm.status).toBe('init')
    expect(vm.autoSave).toBe(true)

    // no buttons expected in AutoSave Mode (which is default)
    expect(wrapper.findAll({ name: 'VBtn' })).toHaveLength(0)
  })

  test('calls api.patch after onInput was triggered', async () => {
    const config = createConfig()
    const patchSpy = jest.spyOn(config.mocks.api, 'patch')

    const wrapper = shallowMount(ApiWrapper, config)
    const vm = wrapper.vm

    const newValue = 'new value'
    const newValueFromApi = 'NEW VALUE'

    vm.onInput(newValue)

    // value (from outside) is still the same
    expect(vm.value).toBe(config.propsData.value)

    // local Value has changed and is dirty
    expect(vm.dirty).toBe(true)
    expect(vm.localValue).toBe(newValue)

    // resolve lodash debounced
    jest.runAllTimers()

    // saving started
    expect(vm.isSaving).toBe(true)
    expect(vm.dirty).toBe(false)
    expect(vm.status).toBe('saving')

    // API patch method called
    expect(patchSpy).toBeCalledTimes(1)
    expect(patchSpy).toBeCalledWith(config.propsData.uri, { [config.propsData.fieldname]: newValue })

    // wait for patch promise to resolve
    await flushPromises()

    // feedback changed return value from API & make sure it's taken over to localValue
    wrapper.setProps({ value: newValueFromApi })
    await wrapper.vm.$nextTick()
    expect(vm.localValue).toBe(newValueFromApi)

    // success state
    expect(vm.status).toBe('success')

    // wait for timer
    jest.runAllTimers()

    // again in init state
    expect(vm.status).toBe('init')
  })
})

describe('Testing manual save mode', () => {
  beforeEach(() => {
    vuetify = new Vuetify()
  })

  test('init correctly with default values', () => {
    const config = createConfig()
    config.propsData.autoSave = false
    const wrapper = shallowMount(ApiWrapper, config)
    const vm = wrapper.vm

    expect(vm.value).toBe(config.propsData.value)
    expect(vm.dirty).toBe(false)
    expect(vm.isSaving).toBe(false)
    expect(vm.status).toBe('init')
    expect(vm.autoSave).toBe(false)

    // expecting both a reset button & a save button in manual mode
    expect(wrapper.findAll({ name: 'VBtn' })).toHaveLength(2)
  })
})
