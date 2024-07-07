import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'
import dayjs from '@/plugins/dayjs.js'

import ETimeField from '@/components/form/base/ETimeField.vue'
import { mount as mountComponent } from '@vue/test-utils'

Vue.use(Vuetify)
Vue.use(dayjs)
Vue.use(formBaseComponents)

describe('An ETimeField', () => {
  let vuetify

  const mount = (options) => {
    const app = Vue.component('App', {
      components: { ETimeField },
      data: function () {
        return {
          data: null,
        }
      },
      template: `<div data-app><e-time-field label="test" v-model="data"/></div>`,
    })
    return mountComponent(app, { vuetify, attachTo: document.body, ...options })
  }

  beforeEach(() => {
    vuetify = new Vuetify()
  })

  test.each([
    ['8', '08:00'],
    [' 8 ', '08:00'],
    ['13', '13:00'],
    ['25', '02:50'],
    ['29', '02:09'],
    ['84', '08:40'],
    ['001', '00:10'],
    ['007', '00:07'],
    ['013', '01:30'],
    ['090', '09:00'],
    ['130', '13:00'],
    ['139', '13:09'],
    ['240', '02:40'],
    ['951', '09:51'],
    ['1230', '12:30'],
    ['2400', null],
    ['9:20', '09:20'],
    ['9:2', '09:02'],
    ['19:34', '19:34'],
    ['19.34', '19:34'],
    ['19,34', '19:34'],
    ['19h34', '19:34'],
    ['19 h 34', '19:34'],
    ['', null],
  ])('parses "%s" as "%s"', async (string, expected) => {
    const wrapper = mount()
    const input = wrapper.find('input')

    input.element.value = `${string}`
    await input.trigger('input')

    expect(wrapper.vm.data).toBe(expected)
  })
})
