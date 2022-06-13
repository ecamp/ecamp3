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

  const mount = (options) => {
    const app = Vue.component('App', {
      components: { ESwitch },
      data: function () {
        return {
          data: null,
        }
      },
      template: `
        <div data-app>
          <e-switch v-model="data"/>
        </div>
      `,
    })
    return mountComponent(app, { vuetify, attachTo: document.body, ...options })
  }
  beforeEach(() => {
    vuetify = new Vuetify()
  })
  test('looks like a switch', async () => {
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
          data: true,
        }
      },
    })
    expect(
      wrapper.find('input[type=checkbox]').element.getAttribute('aria-checked')
    ).toBe('true')
  })

  test('updates switch when vModel changes', async () => {
    const wrapper = mount()
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

  test('updates vModel when user toggles with click', async () => {
    const wrapper = mount()
    const input = wrapper.find('input')

    await input.trigger('click')
    expect(wrapper.vm.data).toBe(true)

    await input.trigger('click')
    expect(wrapper.vm.data).toBe(false)
  })

  test('updates vModel when user toggles with touch swipe', async () => {
    const wrapper = mount()

    touch(wrapper.find('.v-input--selection-controls__ripple')).start(0, 0).end(20, 0)
    expect(wrapper.vm.data).toBe(true)

    jest.resetAllMocks()
    touch(wrapper.find('.v-input--selection-controls__ripple')).start(0, 0).end(-20, 0)
    expect(wrapper.vm.data).toBe(false)
  })

  test('updates vModel when user toggles with keys', async () => {
    const wrapper = mount()
    const input = wrapper.find('input')

    input.trigger('keydown.right')
    expect(wrapper.vm.data).toBe(true)

    input.trigger('keydown.right')
    expect(wrapper.vm.data).toBe(true)

    jest.resetAllMocks()
    input.trigger('keydown.left')
    expect(wrapper.vm.data).toBe(false)
  })
})
