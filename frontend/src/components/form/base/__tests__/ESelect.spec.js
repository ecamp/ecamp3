import Vue from 'vue'
import Vuetify from 'vuetify'

import { formBaseComponents } from '@/plugins'

import { mount as mountComponent } from '@vue/test-utils'
import ESelect from '../ESelect'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('An ESelect', () => {
  let vuetify

  const FIRST_OPTION = {
    value: 1,
    text: 'firstOption',
  }
  const SECOND_OPTION = {
    value: '2',
    text: 'secondOption',
  }
  const THIRD_OPTION = {
    value: { key: 'value', array: [1, 2, 3], nestedObject: { key: 'value' } },
    text: 'thirdOption',
  }
  const selectValues = [FIRST_OPTION, SECOND_OPTION, THIRD_OPTION]

  const mount = (options) => {
    const app = Vue.component('App', {
      components: { ESelect },
      data: function () {
        return {
          selectValues: selectValues,
          data: null,
        }
      },
      template: `
          <div data-app>
          <e-select :items="selectValues" v-model="data"/>
          </div>
        `,
    })
    return mountComponent(app, { vuetify, attachTo: document.body, ...options })
  }

  beforeEach(() => {
    vuetify = new Vuetify()
  })
  test('looks like a dropdown', async () => {
    const wrapper = mount()
    expect(wrapper).toMatchSnapshot('no item selected')

    await wrapper.find('.v-input__slot').trigger('click')
    expect(wrapper).toMatchSnapshot('dropdown open')

    await wrapper.findAll('[role="option"]').at(0).trigger('click')
    expect(wrapper).toMatchSnapshot('dropdown closed with selected value')

    await wrapper.find('.v-input__slot').trigger('click')
    expect(wrapper).toMatchSnapshot('dropdown open with selected value')
  })

  test('update viewmodel with selected value', async () => {
    const wrapper = mount()
    expect(wrapper.vm.data).toBeNull()

    await wrapper.find('.v-input__slot').trigger('click')
    await wrapper.findAll('[role="option"]').at(0).trigger('click')
    expect(wrapper.vm.data).toBe(FIRST_OPTION.value)

    await wrapper.find('.v-input__slot').trigger('click')
    await wrapper.findAll('[role="option"]').at(2).trigger('click')
    expect(wrapper.vm.data).toBe(THIRD_OPTION.value)
  })

  test('update selected value with viewmodel', async () => {
    const wrapper = mount()

    await wrapper.setData({ data: SECOND_OPTION.value })
    expect(wrapper.html()).toContain(SECOND_OPTION.text)
    expect(wrapper.html()).not.toContain(FIRST_OPTION.text)

    await wrapper.setData({ data: FIRST_OPTION.value })
    expect(wrapper.html()).toContain(FIRST_OPTION.text)
    expect(wrapper.html()).not.toContain(SECOND_OPTION.text)
  })

  test('update selected value after it was open', async () => {
    const wrapper = mount()

    await wrapper.find('.v-input__slot').trigger('click')
    await wrapper.findAll('[role="option"]').at(0).trigger('click')
    expect(wrapper.vm.data).toBe(FIRST_OPTION.value)

    await wrapper.setData({ data: SECOND_OPTION.value })
    expect(
      wrapper.findAll('[role="option"]').at(1).element.getAttribute('aria-selected')
    ).toBe('true')
    expect(
      wrapper.findAll('[role="option"]').at(0).element.getAttribute('aria-selected')
    ).not.toBe('true')
  })
})
