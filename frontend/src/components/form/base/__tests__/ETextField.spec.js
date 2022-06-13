import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'

import { mount as mountComponent } from '@vue/test-utils'
import ETextField from '../ETextField'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('An ETextField', () => {
  let vuetify

  const mount = (options) => {
    const app = Vue.component('App', {
      components: { ETextField },
      data: function () {
        return {
          data: null,
        }
      },
      template: `
        <div data-app>
        <e-text-field v-model="data"/>
        </div>`,
    })
    return mountComponent(app, { vuetify, attachTo: document.body, ...options })
  }
  beforeEach(() => {
    vuetify = new Vuetify()
  })
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
