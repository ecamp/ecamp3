import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'

import { mount as mountComponent } from '@vue/test-utils'
import ECheckbox from '../ECheckbox'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('An ECheckbox', () => {
  let vuetify

  const mount = (options) => {
    const app = Vue.component('App', {
      components: { ECheckbox },
      data: function () {
        return {
          data: null
        }
      },
      template: `
        <div data-app>
          <e-checkbox v-model="data"/>
        </div>
      `
    })
    return mountComponent(app, { vuetify, attachTo: document.body, ...options })
  }
  beforeEach(() => {
    vuetify = new Vuetify()
  })
  test('looks like a checkbox', async () => {
    const wrapper = mount()
    await wrapper.setData({ data: false })
    expect(wrapper).toMatchSnapshot('unchecked')

    await wrapper.setData({ data: true })
    expect(wrapper).toMatchSnapshot('checked')
  })

  test('is checked when initialized checked', () => {
    const wrapper = mount({
      data: function () {
        return {
          data: true
        }
      }
    })
    expect(
      wrapper.find('input[type=checkbox]').element.getAttribute('aria-checked')
    ).toBe('true')
  })

  test('updates checkbox when vModel changes', async () => {
    const wrapper = mount()
    await wrapper.setData({ data: false })
    expect(
      wrapper.find('input[type=checkbox]').element.getAttribute('aria-checked')
    ).toBe('false')

    await wrapper.setData({ data: true })
    expect(
      wrapper.find('input[type=checkbox]').element.getAttribute('aria-checked')
    ).toBe('true')

    await wrapper.setData({ data: false })
    expect(
      wrapper.find('input[type=checkbox]').element.getAttribute('aria-checked')
    ).toBe('false')
  })

  test('updates vModel when user clicks on checkbox', async () => {
    const wrapper = mount()
    const input = wrapper.find('input')

    await input.trigger('click')
    expect(wrapper.vm.data).toBe(true)

    jest.resetAllMocks()
    await input.trigger('click')
    expect(wrapper.vm.data).toBe(false)
  })
})
