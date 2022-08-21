import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'

import { mount as mountComponent } from '@vue/test-utils'
import ERichtext from '../ERichtext'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('An ERichtext', () => {
  let vuetify

  const mount = (options) => {
    const app = Vue.component('App', {
      components: { ERichtext },
      data: function () {
        return {
          data: null,
        }
      },
      template: `
        <div data-app>
        <e-richtext v-model="data"/>
        </div>`,
    })
    return mountComponent(app, { vuetify, attachTo: document.body, ...options })
  }
  beforeEach(() => {
    vuetify = new Vuetify()
  })
  test('looks like a richtext field', async () => {
    const wrapper = mount()
    expect(wrapper).toMatchSnapshot('empty')

    await wrapper.setData({
      data: `My text
    with newlines
    and <strong>bold</strong>`,
    })
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
})
