// Libraries
import Vue from 'vue'
import Vuetify from 'vuetify'
import flushPromises from 'flush-promises'
import { createLocalVue, shallowMount } from '@vue/test-utils'
import veeValidatePlugin from '@/plugins/veeValidate'
import ApiWrapper from '../ApiWrapper.vue'
import { VForm, VBtn } from 'vuetify/lib'
import { ValidationObserver } from 'vee-validate'

jest.mock('lodash')
const { cloneDeep } = jest.requireActual('lodash')

/*
jest.mock('vee-validate', () => ({
  validate: jest.fn().mockResolvedValue({
    valid: true,
    errors: []
  })
})) */

jest.useFakeTimers()

Vue.use(Vuetify)
Vue.use(veeValidatePlugin)
let vuetify

// creates a mock Promise which resolves within 100ms with value
function mockPromiseResolving (value) {
  return new Promise((resolve, reject) => {
    const timer = setTimeout(() => {
      clearTimeout(timer)
      resolve(value)
    }, 100)
  })
}

// creates a mock Promise which rejects within 100ms with value
/*
function mockPromiseRejecting (value) {
  return new Promise((resolve, reject) => {
    const timer = setTimeout(() => {
      clearTimeout(timer)
      reject(value)
    }, 100)
  })
} */

// config factory
function createConfig (overrides) {
  const mocks = {
    api: {
      patch: () => mockPromiseResolving({}),
      get: () => {
        return {
          _meta: {
            load: () => mockPromiseResolving({})
          }
        }
      }
    }
  }

  const propsData = {
    value: 'Test Value',
    fieldname: 'testField',
    uri: '/testEntity/123',
    label: 'Test Field'
  }

  const stubs = {
    VForm,
    VBtn,
    ValidationObserver
  }

  const scopedSlots = {
    default: '<input type="text" name="dummyField" id="dummyField" :value="props.localValue" />'
  }

  const localVue = createLocalVue()

  return cloneDeep(Object.assign({ mocks, propsData, vuetify, stubs, scopedSlots, localVue }, overrides))
}

/**
 * AutoSave = true
 * External value
 */
describe('Testing ApiWrapper [autoSave=true;  manual external value]', () => {
  let wrapper
  let vm
  let config
  let apiPatch
  let validate

  beforeEach(() => {
    vuetify = new Vuetify()

    config = createConfig()
    wrapper = shallowMount(ApiWrapper, config)
    vm = wrapper.vm

    apiPatch = jest.spyOn(config.mocks.api, 'patch')

    // mock validation Promise
    validate = jest.spyOn(vm.$refs.validationObserver, 'validate')
    validate.mockImplementation(() => mockPromiseResolving(true))
  })

  afterEach(() => {
    jest.restoreAllMocks()
  })

  test('init correctly with default values', () => {
    expect(vm.value).toBe(config.propsData.value)
    expect(vm.dirty).toBe(false)
    expect(vm.isSaving).toBe(false)
    expect(vm.isLoading).toBe(false)
    expect(vm.status).toBe('init')
    expect(vm.autoSave).toBe(true)

    // no buttons expected in AutoSave Mode (which is default)
    expect(wrapper.findAllComponents({ name: 'VBtn' })).toHaveLength(0)
  })

  test('calls api.patch after onInput was triggered', async () => {
    const newValue = 'new value'
    const newValueFromApi = 'NEW VALUE'

    vm.onInput(newValue)

    // value (from outside) is still the same
    expect(vm.value).toBe(config.propsData.value)

    // local Value has changed and is dirty
    expect(vm.dirty).toBe(true)
    expect(vm.localValue).toBe(newValue)

    // resolve lodash debounced
    jest.advanceTimersByTime(100)
    await flushPromises()

    // await validation Promise
    jest.advanceTimersByTime(100)
    await flushPromises()

    // saving started
    expect(vm.isSaving).toBe(true)
    expect(vm.dirty).toBe(false)
    expect(vm.status).toBe('saving')

    // API patch method called
    expect(apiPatch).toBeCalledTimes(1)
    expect(apiPatch).toBeCalledWith(config.propsData.uri, { [config.propsData.fieldname]: newValue })

    // wait for patch promise to resolve
    jest.advanceTimersByTime(100)
    await flushPromises()

    // feedback changed return value from API & make sure it's taken over to localValue
    wrapper.setProps({ value: newValueFromApi })
    await wrapper.vm.$nextTick()
    expect(vm.localValue).toBe(newValueFromApi)

    // success state
    expect(vm.status).toBe('success')

    // wait for success icon timer to finish
    jest.advanceTimersByTime(2000)
    await flushPromises()

    // again in init state
    expect(vm.status).toBe('init')
  })

  test('avoid double triggering of save for enter key', async done => {
    // given
    const input = wrapper.find('input')

    // when
    vm.onInput('new value')
    input.trigger('submit') // trigger submit event (simulates enter key)
    jest.runAllTimers() // resolve lodash debounced
    await flushPromises() // resolve validation

    // then
    expect(apiPatch).toHaveBeenCalledTimes(1)

    done()
  })

  test('shows server error if api.patch failed', async () => {
    // given
    apiPatch.mockRejectedValueOnce(new Error('server error'))

    // when
    vm.onInput('new value') // Trigger patch
    jest.runAllTimers() // resolve lodash debounced
    await flushPromises() // wait for patch promise to resolve

    // then
    expect(vm.hasServerError).toBe(true)
    expect(vm.errorMessages).toContain('server error')
  })

  test('can process server validation error', async () => {
    const validationMsg = 'The input is less than 10 characters long'
    apiPatch.mockRejectedValueOnce(new Error(validationMsg))

    // when
    vm.onInput('new value') // Trigger patch
    jest.runAllTimers() // resolve lodash debounced
    await flushPromises() // wait for patch promise to resolve

    // then
    expect(vm.hasServerError).toBe(true)
    expect(vm.errorMessages).toContain(validationMsg)
  })

  /*
  test('shows error if `required` validation fails', async done => {
    // given
    wrapper.setProps({ required: true })

    // when
    await vm.onInput('')

    // then
    expect(vm.hasValidationError).toBe(true)
    expect(vm.errorMessages[0]).toMatch('is required')

    done()
  }) */

  /*
  test('shows error if arbitrary validation fails & aborts save', async done => {
    // given
    wrapper.setProps({ validation: 'min:3|myOwnValidationRule' })
    validate.mockResolvedValue({ valid: false, errors: ['Validation failed'] })

    // when
    await vm.onInput('any value')

    // then
    expect(validate).toHaveBeenCalledWith('any value', 'min:3|myOwnValidationRule', { name: 'Test Field' })
    expect(vm.hasValidationError).toBe(true)
    expect(vm.errorMessages[0]).toMatch('Validation failed')

    // when
    vm.save()

    // then
    expect(apiPatch).not.toHaveBeenCalled()

    done()
  })

  test('clears error if arbitrary validation succedes', async done => {
    // given
    wrapper.setProps({ validation: 'required' })
    wrapper.vm.hasValidationError = true
    validate.mockResolvedValue({ valid: true, errors: [] })

    // when
    await vm.onInput('any value')

    // then
    expect(vm.hasValidationError).toBe(false)
    expect(vm.errorMessages).toHaveLength(0)

    done()
  }) */
})

/**
 * AutoSave = true
 * Value from API
 */
describe('Testing ApiWrapper [autoSave=true; value from API]', () => {
  let wrapper
  let vm
  let config
  // let apiPatch
  let apiGet

  beforeEach(() => {
    vuetify = new Vuetify()

    config = createConfig()
    delete config.propsData.value

    // apiPatch = jest.spyOn(config.mocks.api, 'patch')
    apiGet = jest.spyOn(config.mocks.api, 'get')

    apiGet.mockReturnValue({
      [config.propsData.fieldname]: 'api value',
      _meta: {
        load: Promise.resolve()
      }
    })

    wrapper = shallowMount(ApiWrapper, config)
    vm = wrapper.vm
  })

  test('loads value from API (fieldname = primitive value)', async () => {
    // given
    const loadingValue = () => {}
    loadingValue.loading = true
    apiGet.mockReturnValue({
      [config.propsData.fieldname]: loadingValue,
      _meta: {
        loading: true,
        load: Promise.resolve()
      }
    })

    // when
    wrapper = shallowMount(ApiWrapper, config)
    vm = wrapper.vm

    // then
    expect(vm.isLoading).toBe(true)
    expect(vm.localValue).toBe(null)

    // given
    apiGet.mockReturnValue({
      [config.propsData.fieldname]: 'api value',
      _meta: {
        load: Promise.resolve()
      }
    })

    // when
    await flushPromises() // wait for load promise to resolve

    // then
    expect(vm.hasFinishedLoading).toBe(true)
    expect(vm.isLoading).toBe(false)
    expect(vm.localValue).toBe('api value')
  })

  test('shows error when loading value from API fails', async () => {
    // given
    const loadingValue = () => {}
    loadingValue._meta = { loading: true }
    apiGet.mockReturnValue({
      [config.propsData.fieldname]: loadingValue,
      _meta: {
        load: Promise.reject(new Error('loading error')),
        loading: true
      }
    })
    wrapper = shallowMount(ApiWrapper, config)
    vm = wrapper.vm

    // when
    await flushPromises() // wait for load promise to resolve

    // then
    expect(vm.hasFinishedLoading).toBe(false)
    expect(vm.isLoading).toBe(false)
    expect(vm.hasLoadingError).toBe(true)
    expect(vm.errorMessages[0]).toMatch('loading error')
  })

  test('loads IRI from API (fieldname = embedded entity)', async () => {
    // given
    const loadingValue = () => {}
    loadingValue._meta = { loading: true }
    apiGet.mockReturnValue({
      [config.propsData.fieldname]: loadingValue,
      _meta: {
        load: Promise.resolve(),
        loading: true
      }
    })

    wrapper = shallowMount(ApiWrapper, config)
    vm = wrapper.vm

    apiGet.mockReturnValue({
      [config.propsData.fieldname]: () => ({
        _meta: {
          self: '/iri/5'
        }
      }),
      _meta: {
        load: Promise.resolve()
      }
    })

    // when
    await flushPromises() // wait for load promise to resolve

    // then
    expect(vm.hasFinishedLoading).toBe(true)
    expect(vm.isLoading).toBe(false)
    expect(vm.localValue).toBe('/iri/5')
  })

  test('loads array of IRIs from API (fieldname = embedded collection)', async () => {
    // given
    const loadingValue = () => {}
    loadingValue.loading = true
    apiGet.mockReturnValue({
      [config.propsData.fieldname]: loadingValue,
      _meta: {
        load: Promise.resolve()
      }
    })

    wrapper = shallowMount(ApiWrapper, config)
    vm = wrapper.vm

    apiGet.mockReturnValue({
      [config.propsData.fieldname]: () => ({
        items: [
          {
            _meta: {
              self: '/iri/5'
            }
          },
          {
            _meta: {
              self: '/iri/6'
            }
          }
        ]
      }),
      _meta: {
        load: Promise.resolve()
      }
    })

    // when
    await flushPromises() // wait for load promise to resolve

    // then
    expect(vm.hasFinishedLoading).toBe(true)
    expect(vm.isLoading).toBe(false)
    expect(vm.localValue).toStrictEqual(['/iri/5', '/iri/6'])
  })
})

/**
 * Manual mode
 */
describe('Testing ApiWrapper [autoSave=false]', () => {
  let wrapper
  let vm
  let config
  let apiPatch

  beforeEach(() => {
    vuetify = new Vuetify()

    config = createConfig()
    config.propsData.autoSave = false

    wrapper = shallowMount(ApiWrapper, config)
    vm = wrapper.vm

    apiPatch = jest.spyOn(config.mocks.api, 'patch')
  })

  test('init correctly with default values', () => {
    expect(vm.value).toBe(config.propsData.value)
    expect(vm.dirty).toBe(false)
    expect(vm.isSaving).toBe(false)
    expect(vm.status).toBe('init')
    expect(vm.autoSave).toBe(false)
  })

  test('clears dirty flag when local value matches external value', async () => {
    // local change
    vm.onInput('new local value')
    expect(vm.dirty).toBe(true)

    // new value from external --> local value will not be changed
    wrapper.setProps({ value: 'new external value #1' })
    expect(vm.localValue).toBe('new local value')

    // local change to same value as external value
    vm.onInput('new external value #1')
    await vm.$nextTick() // needed for watcher to trigger
    expect(vm.dirty).toBe(false)

    // new value from external --> local value will be changed
    wrapper.setProps({ value: 'new external value #2' })
    await vm.$nextTick() // needed for watcher to trigger
    expect(vm.localValue).toBe('new external value #2')
  })

  test('resets value and errors when `reset` is called', async () => {
    // when
    vm.onInput('new local value')
    // vm.hasValidationError = true

    // then
    expect(vm.dirty).toBe(true)
    expect(vm.localValue).toBe('new local value')

    // when
    vm.reset()

    // then
    expect(vm.dirty).toBe(false)
    expect(vm.localValue).toBe('Test Value')
    // expect(vm.hasValidationError).toBe(false)
  })

  test('trigger save with enter key', async () => {
    // given
    const input = wrapper.find('input')

    // when
    input.trigger('submit')
    await vm.$nextTick()
    await flushPromises() // resolve validation

    // then
    expect(apiPatch).toHaveBeenCalled()
  })

  test('abort save in readonly mode', () => {
    // given
    wrapper.setProps({ readonly: true })

    // when
    vm.save()

    // then
    expect(apiPatch).not.toHaveBeenCalled()
  })

  test('abort save in disabled mode', () => {
    // given
    wrapper.setProps({ disabled: true })

    // when
    vm.save()

    // then
    expect(apiPatch).not.toHaveBeenCalled()
  })
})
