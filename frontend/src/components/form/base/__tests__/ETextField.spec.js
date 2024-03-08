import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'

import { mount as mountComponent } from '@vue/test-utils'
import ETextField from '../ETextField.vue'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('An ETextField', () => {
  let vuetify

  const mount = (options, number = false) => {
    const app = Vue.component('App', {
      components: { ETextField },
      data: function () {
        return {
          data: null,
        }
      },
      template: number
        ? `
        <div data-app>
        <e-text-field label="test" v-model.number="data"/>
        </div>`
        : `
        <div data-app>
        <e-text-field label="test" v-model="data"/>
        </div>`,
    })
    return mountComponent(app, { vuetify, attachTo: document.body, ...options })
  }

  beforeEach(() => {
    vuetify = new Vuetify()
  })

  describe('text', () => {
    test('looks like a textfield', async () => {
      const wrapper = mount()
      expect(wrapper).toMatchSnapshot('empty')

      await wrapper.setData({ data: 'MyText' })
      expect(wrapper).toMatchSnapshot('with text')
    })

    test('updates text when vModel changes', async () => {
      const wrapper = mount()
      expect(wrapper.find('.e-form-container').element.getAttribute('value')).toBeNull()

      const firstText = 'myText'
      await wrapper.setData({ data: firstText })
      expect(wrapper.find('.e-form-container').element.getAttribute('value')).toBe(
        firstText
      )

      const secondText = 'myText2'
      await wrapper.setData({ data: secondText })
      expect(wrapper.find('.e-form-container').element.getAttribute('value')).toBe(
        secondText
      )
    })

    test('updates vModel when value of input field changes', async () => {
      const wrapper = mount()
      const input = wrapper.find('input')
      const text = 'bla'

      input.element.value = text
      await input.trigger('input')

      expect(wrapper.vm.data).toBe(text)
    })
  })

  describe('number', () => {
    test('looks like a textfield', async () => {
      const wrapper = mount({}, true)
      expect(wrapper).toMatchSnapshot('empty')

      await wrapper.setData({ data: 3.14 })
      expect(wrapper).toMatchSnapshot('with text')
    })

    test('updates text when vModel changes', async () => {
      const wrapper = mount({}, true)
      expect(wrapper.find('.e-form-container').element.getAttribute('value')).toBeNull()

      const firstText = 0
      await wrapper.setData({ data: firstText })
      expect(wrapper.find('.e-form-container').element.getAttribute('value')).toBe(
        `${firstText}`
      )

      const secondText = 3.14
      await wrapper.setData({ data: secondText })
      expect(wrapper.find('.e-form-container').element.getAttribute('value')).toBe(
        `${secondText}`
      )
    })

    test('updates vModel when value of input field changes', async () => {
      const wrapper = mount({}, true)
      const input = wrapper.find('input')
      const text = 3.14

      input.element.value = text
      await input.trigger('input')

      expect(wrapper.vm.data).toBe(text)
    })
  })
})
