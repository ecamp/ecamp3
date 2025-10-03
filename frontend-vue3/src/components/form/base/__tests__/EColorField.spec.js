import { describe, beforeEach, test, expect } from 'vitest'
import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'

import EColorField from '@/components/form/base/EColorField.vue'
import { mount as mountComponent } from '@vue/test-utils'
import { ColorSpace, sRGB } from 'colorjs.io/fn'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

ColorSpace.register(sRGB)

describe('An EColorField', () => {
  let vuetify

  const mount = (options) => {
    const app = Vue.component('App', {
      components: { EColorField },
      data: function () {
        return {
          data: null,
        }
      },
      methods: {
        parse: (value) => {
          return value === 'true' ? true : value === 'false' ? false : null
        },
        format: (value) => {
          return value === null ? '' : `${value}`
        },
      },
      template: `<div data-app><e-color-field label="test" v-model="data"/></div>`,
    })
    return mountComponent(app, { vuetify, attachTo: document.body, ...options })
  }

  beforeEach(() => {
    vuetify = new Vuetify()
  })

  test('looks like a textfield', async () => {
    const wrapper = mount()
    expect(wrapper).toMatchSnapshot('empty')

    await wrapper.setData({ data: '#FF0000' })
    expect(wrapper).toMatchSnapshot('with text')
  })

  test('updates text when vModel changes', async () => {
    const wrapper = mount()
    const input = wrapper.find('input').element
    expect(input.value).toBeDefined()

    const firstValue = '#A1E1E1'
    await wrapper.setData({ data: firstValue })
    expect(input.value).toBe(`${firstValue}`)

    const secondValue = '#FF00FF'
    await wrapper.setData({ data: secondValue })
    expect(input.value).toBe(`${secondValue}`)
  })

  test('updates vModel when value of input field changes', async () => {
    const wrapper = mount()
    const input = wrapper.find('input')
    const value = '#123456'

    input.element.value = `${value}`
    await input.trigger('input')

    expect(wrapper.vm.data).toBe(value)
  })

  test.each([
    ['#00FF00', '#00FF00'],
    ['red', '#FF0000'],
    ['', null],
  ])('parses "%s" as "%s"', async (string, expected) => {
    const wrapper = mount()
    const input = wrapper.find('input')

    input.element.value = `${string}`
    await input.trigger('input')

    expect(wrapper.vm.data).toBe(expected)
  })
})
