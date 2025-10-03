import { describe, beforeEach, expect, test } from 'vitest'
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
    [' 9 ', '09:00'],
    ['2400', null],
    ['19,34', '19:34'],
    ['19h34', '19:34'],
    ['', null],
  ])('parses "%s" as "%s"', async (string, expected) => {
    const wrapper = mount()
    const input = wrapper.find('input')

    input.element.value = `${string}`
    await input.trigger('input')

    expect(wrapper.vm.data).toBe(expected)
  })
})
