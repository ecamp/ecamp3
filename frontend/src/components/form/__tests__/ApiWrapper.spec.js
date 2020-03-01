// Libraries
import Vue from 'vue'
import Vuetify from 'vuetify'

import { shallowMount } from '@vue/test-utils'
import ApiWrapper from '../ApiWrapper.vue'

jest.mock('lodash')

Vue.use(Vuetify)

jest.useFakeTimers()

describe('Testing autoSave mode', () => {
  let vuetify
  let wrapper
  let vm

  const props = {
    value: 'Test Value',
    fieldname: 'test-field',
    uri: 'test-field/123'
  }

  const patchPromise = new Promise(resolve => {
    process.nextTick(() => {
      resolve()
    })
  })

  const mockApi = {
    patch: jest.fn(() => patchPromise)
  }

  beforeEach(() => {
    vuetify = new Vuetify()

    wrapper = shallowMount(ApiWrapper, {
      vuetify,
      propsData: props,
      mocks: {
        api: mockApi
      }
    })

    vm = wrapper.vm
  })

  test('init correctly with default values', () => {
    expect(vm.value).toBe(props.value)
    expect(vm.dirty).toBe(false)
    expect(vm.isSaving).toBe(false)
    expect(vm.status).toBe('init')
    expect(vm.autoSave).toBe(true)
    expect(vm.editing).toBe(true)

    // no buttons expected in AutoSave Mode (which is default)
    expect(wrapper.findAll({ name: 'VBtn' })).toHaveLength(0)
  })

  test('calls api.patch after onInput was triggered', async () => {
    const newValue = 'new value'

    await vm.onInput(newValue)

    // value (from outside) is still the same
    expect(vm.value).toBe(props.value)

    // local Value has changed and is dirty
    expect(vm.dirty).toBe(true)
    expect(vm.localValue).toBe(newValue)

    // saving started
    expect(vm.isSaving).toBe(true)
    expect(vm.status).toBe('saving')

    // API patch method called
    expect(mockApi.patch).toBeCalledTimes(1)
    expect(mockApi.patch).toBeCalledWith(props.uri, { [props.fieldname]: newValue })

    // watit for patch promise to resolve
    await
    jest.runAllTimers()

    // success state
    expect(vm.status).toBe('success')

    // wait for timer
    jest.runAllTimers()

    // again in init state
    expect(vm.status).toBe('init')
  })
})

describe('Testing manual save mode', () => {
  let vuetify

  beforeEach(() => {
    vuetify = new Vuetify()
  })

  test('init correctly with default values', () => {
    const props = {
      value: 'Test Value',
      fieldname: 'test-field',
      uri: 'test-field/123',
      autoSave: false
    }

    const wrapper = shallowMount(ApiWrapper, {
      vuetify,
      propsData: props
    })

    const vm = wrapper.vm

    expect(vm.value).toBe(props.value)
    expect(vm.dirty).toBe(false)
    expect(vm.isSaving).toBe(false)
    expect(vm.status).toBe('init')
    expect(vm.autoSave).toBe(false)
    expect(vm.editing).toBe(true)

    // expecting both a reset button & a save button in manual mode
    expect(wrapper.findAll({ name: 'VBtn' })).toHaveLength(2)
  })
})
