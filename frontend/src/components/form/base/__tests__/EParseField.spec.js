import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'

import EParseField from '@/components/form/base/EParseField.vue'
import { mount as mountComponent } from '@vue/test-utils'
import { screen } from '@testing-library/vue'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('An EParseField', () => {
  let vuetify

  const mount = (options) => {
    const app = Vue.component('App', {
      components: { EParseField },
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
      template: `
        <div data-app>
          <e-parse-field label="test" :parse="parse" :format="format" v-model="data" :value="data">
            ${options?.children}
          </e-parse-field>
        </div>
      `,
    })
    return mountComponent(app, { vuetify, attachTo: document.body, ...options })
  }

  beforeEach(() => {
    vuetify = new Vuetify()
  })

  test('looks like a textfield', async () => {
    const wrapper = mount()
    expect(wrapper).toMatchSnapshot('empty')

    await wrapper.setData({ data: true })
    expect(wrapper).toMatchSnapshot('with text')
  })

  test('updates text when vModel changes', async () => {
    const wrapper = mount()
    const input = wrapper.find('input').element
    expect(input.value).toBeDefined()

    const firstValue = true
    await wrapper.setData({ data: firstValue })
    expect(input.value).toBe(`${firstValue}`)

    const secondValue = false
    await wrapper.setData({ data: secondValue })
    expect(input.value).toBe(`${secondValue}`)
  })

  test('updates vModel when value of input field changes', async () => {
    const wrapper = mount()
    const input = wrapper.find('input')
    const value = true

    input.element.value = `${value}`
    await input.trigger('input')

    expect(wrapper.vm.data).toBe(value)
  })

  test.each([
    ['true', true],
    ['false', false],
    ['', null],
    ['s', null],
    ['0', null],
    ['1', null],
  ])('parses "%s" as "%s"', async (string, expected) => {
    const wrapper = mount()
    const input = wrapper.find('input')

    input.element.value = `${string}`
    await input.trigger('input')

    expect(wrapper.vm.data).toBe(expected)
  })

  test('allows to use the append slot', async () => {
    mount({
      children: `
        <template #append>
          <span>append</span>
        </template>
      `,
    })

    expect(await screen.findByText('append')).toBeVisible()
  })
})
