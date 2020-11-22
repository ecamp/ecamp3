import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'

import { mount as mountComponent } from '@vue/test-utils'
import ECheckbox from '../ECheckbox'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('An ECheckbox', () => {
  let vuetify

  const mount = (options) => mountComponent(ECheckbox, { vuetify, ...options })
  beforeEach(() => {
    vuetify = new Vuetify()
  })
  test('looks like a checkbox', async () => {
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

  test('updates checkbox when vModel changes', async () => {
    const wrapper = mount()
    expect(wrapper.find('input[type=checkbox]').element.getAttribute('vmodel')).toBeNull()

    await wrapper.setProps({ vModel: true })
    expect(wrapper.find('input[type=checkbox]').element.getAttribute('vmodel')).toBe('true')

    await wrapper.setProps({ vModel: false })
    expect(wrapper.find('input[type=checkbox]').element.getAttribute('vmodel')).toBeNull()
  })

  test('updates vModel when user clicks on checkbox', async () => {
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
})
