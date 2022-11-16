import Vue from 'vue'
import Vuetify from 'vuetify'

import i18n from '@/plugins/i18n'
import formBaseComponents from '@/plugins/formBaseComponents'
import dayjs from '@/plugins/dayjs'

import { mount as mountComponent } from '@vue/test-utils'
import EDatePicker from '../EDatePicker'
import { waitForDebounce } from '@/test/util'
import flushPromises from 'flush-promises'

Vue.use(Vuetify)
Vue.use(formBaseComponents)
Vue.use(dayjs)

describe('An EDatePicker', () => {
  let vuetify

  const DATE_1 = '2020-03-01'
  const DATE_2 = '2015-12-31'
  const INVALID_DATE_1 = 'some date'
  const INVALID_DATE_2 = '2026-13-01'

  const localeData = [
    [
      'de',
      {
        date_1: '01.03.2020',
        date_2: '31.12.2015',
        date_3: '24.03.2020',
      },
    ],
    [
      'en',
      {
        date_1: '03/01/2020',
        date_2: '12/31/2015',
        date_3: '03/24/2020',
      },
    ],
  ]

  const mount = (options) => mountComponent(EDatePicker, { vuetify, i18n, ...options })

  describe.each(localeData)('in locale %s', (locale, data) => {
    beforeEach(() => {
      i18n.locale = locale
      Vue.dayjs.locale(locale)
      vuetify = new Vuetify()
    })

    test('renders', async () => {
      const wrapper = mount({
        propsData: {
          value: DATE_1,
        },
      })
      await flushPromises()
      expect(wrapper.find('input[type=text]').element.value).toBe(data.date_1)
    })

    test('looks like a date picker', async () => {
      const wrapper = mountComponent(
        {
          data: () => ({ date: DATE_1 }),
          template: '<div data-app><e-date-picker v-model="date"></e-date-picker></div>',
          components: { 'e-date-picker': EDatePicker },
        },
        {
          vuetify,
          attachTo: document.body,
          i18n,
        }
      )
      await waitForDebounce()
      expect(wrapper).toMatchSnapshot('pickerclosed')
      await wrapper.find('button').trigger('click')
      expect(wrapper).toMatchSnapshot('pickeropen')
      wrapper.destroy()
    })

    test('updates v-model when the value changes', async () => {
      const wrapper = mountComponent(
        {
          data: () => ({ date: DATE_1 }),
          template: '<div><e-date-picker v-model="date"></e-date-picker></div>',
          components: { 'e-date-picker': EDatePicker },
        },
        {
          vuetify,
          i18n,
        }
      )
      expect(wrapper.vm.date).toBe(DATE_1)
      const inputSpy = jest.fn()
      wrapper.findComponent(EDatePicker).vm.$on('input', (event) => inputSpy(event))
      const input = wrapper.find('input[type=text]')
      await input.setValue(data.date_2)
      await waitForDebounce()
      expect(inputSpy).toBeCalledTimes(1)
      expect(inputSpy).toBeCalledWith(DATE_2)
      expect(wrapper.vm.date).toBe(DATE_2)
    })

    test('validates the input', async () => {
      const wrapper = mount({
        propsData: {
          value: DATE_1,
        },
      })
      const input = wrapper.find('input[type=text]')
      await input.setValue(INVALID_DATE_1)
      await waitForDebounce()
      expect(wrapper.text()).toContain('invalid format')
      await input.setValue(INVALID_DATE_2)
      await waitForDebounce()
      expect(wrapper.text()).toContain('invalid format')
    })

    test('updates its value when a date is picked', async () => {
      const wrapper = mountComponent(
        {
          data: () => ({ date: DATE_1 }),
          template: '<div data-app><e-date-picker v-model="date"></e-date-picker></div>',
          components: { 'e-date-picker': EDatePicker },
        },
        {
          vuetify,
          attachTo: document.body,
          i18n,
        }
      )
      await waitForDebounce()
      // open the date picker
      const openPicker = wrapper.find('button')
      await openPicker.trigger('click')
      // click on day 24 of the month
      await wrapper
        .findAll('button')
        .filter((node) => node.text() === '24')
        .at(0)
        .trigger('click')
      await waitForDebounce()
      expect(wrapper.find('input[type=text]').element.value).toBe(data.date_3)
      wrapper.destroy()
    })
  })
})
