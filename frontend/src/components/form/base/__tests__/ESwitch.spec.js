import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'

import { mount as mountComponent } from '@vue/test-utils'
import ESwitch from '@/components/form/base/ESwitch'
import { touch } from '@/test/util'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('An ESwitch', () => {
  let vuetify

  const mount = (options) => mountComponent(ESwitch, { vuetify, ...options })
  beforeEach(() => {
    vuetify = new Vuetify()
  })
  test('looks like a switch', async () => {
    const wrapper = mount()
    await wrapper.setProps({ vModel: false })
    expect(wrapper.element).toMatchSnapshot('unchecked')

    await wrapper.setProps({ vModel: true })
    expect(wrapper.element).toMatchSnapshot('checked')
  })

  test('is checked when initialized checked', () => {
    const wrapper = mount({
      propsData: {
        vModel: true
      }
    })
    expect(wrapper.find('input[type=checkbox]').element.getAttribute('vmodel')).toBe('true')
  })

  test('updates switch when vModel changes', async () => {
    const wrapper = mount()
    expect(wrapper.find('input[type=checkbox]').element.getAttribute('vmodel')).toBeNull()

    await wrapper.setProps({ vModel: true })
    expect(wrapper.find('input[type=checkbox]').element.getAttribute('vmodel')).toBe('true')

    await wrapper.setProps({ vModel: false })
    expect(wrapper.find('input[type=checkbox]').element.getAttribute('vmodel')).toBeNull()
  })

  test('updates vModel when user toggles with click', async () => {
    const wrapper = mount()
    const input = wrapper.find('input')

    const change = jest.fn()
    wrapper.vm.$on('input', (event) => change(event))

    await input.trigger('click')
    expect(change).toBeCalledTimes(1)
    expect(change).toBeCalledWith(true)

    jest.resetAllMocks()
    await input.trigger('click')
    expect(change).toBeCalledTimes(1)
    expect(change).toBeCalledWith(false)
  })

  test('updates vModel when user toggles with touch swipe', async () => {
    const wrapper = mount()

    const change = jest.fn()
    wrapper.vm.$on('input', (event) => change(event))

    touch(wrapper.find('.v-input--selection-controls__ripple')).start(0, 0).end(20, 0)
    expect(change).toBeCalledWith(true)

    jest.resetAllMocks()
    touch(wrapper.find('.v-input--selection-controls__ripple')).start(0, 0).end(-20, 0)
    expect(change).toBeCalledWith(false)
  })

  test('updates vModel when user toggles with keys', async () => {
    const wrapper = mount()
    const input = wrapper.find('input')

    const change = jest.fn()
    wrapper.vm.$on('input', (event) => change(event))

    input.trigger('keydown.right')
    expect(change).toHaveBeenCalledWith(true)
    expect(change).toHaveBeenCalledTimes(1)

    input.trigger('keydown.right')
    expect(change).toHaveBeenCalledTimes(1)

    jest.resetAllMocks()
    input.trigger('keydown.left')
    expect(change).toHaveBeenCalledWith(false)
    expect(change).toHaveBeenCalledTimes(1)
  })
})
