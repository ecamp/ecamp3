import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'

import { mount as mountComponent } from '@vue/test-utils'
import ETextarea from '../ETextarea'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('An ETextArea', () => {
  let vuetify

  const multiLineText = `
    Here comes a text
    with new lines
    and new lines with \n in them
    and tags <i>a</i>
    `

  const mount = (
    options,
    template = `
        <div data-app>
          <e-textarea v-model="data"/>
        </div>
      `
  ) => {
    const app = Vue.component('App', {
      components: { ETextarea },
      data: () => ({ data: null }),
      template: template
    })
    return mountComponent(app, { vuetify, attachTo: document.body, ...options })
  }
  beforeEach(() => {
    vuetify = new Vuetify()
  })
  test('looks like a textarea', async () => {
    const wrapper = mount()
    expect(wrapper).toMatchSnapshot('notext')

    await wrapper.setData({ data: multiLineText })
    expect(wrapper).toMatchSnapshot('withtext')

    const mountWithMultiLine = mount(
      { data: () => ({ data: multiLineText }) },
      `
        <div data-app>
          <e-textarea v-model="data" multi-line/>
        </div>
      `
    )
    expect(mountWithMultiLine).toMatchSnapshot('multiline')

    const mountAsinControls = mount(
      { data: () => ({ data: multiLineText }) },
      `
        <div data-app>
          <e-textarea
                v-model="data"
                :rows="3"
                auto-grow
          />
        </div>
      `
    )
    expect(mountAsinControls).toMatchSnapshot('mountAsInControls')
  })

  test('updates the text with the viewmodel', async () => {
    const wrapper = mount()
    await wrapper.setData({ data: multiLineText })
    const textWithoutMultiLine = multiLineText
      .replace(/\n\s*/g, ' ')
      .replace('  ', ' ')
      .replace('<i>', '')
      .replace('</i>', '')
      .trim()
    expect(wrapper.find('.editor__content').text()).toBe(textWithoutMultiLine)
    expect(wrapper.find('.e-form-container').element.getAttribute('value')).toBe(
      multiLineText
    )
    expect(wrapper.vm.data).toBe(multiLineText)
  })
})
