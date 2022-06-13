import Vue from 'vue'
import Vuetify from 'vuetify'

import i18n from '@/plugins/i18n'
import formBaseComponents from '@/plugins/formBaseComponents'
import dayjs from '@/plugins/dayjs'

import { mount as mountComponent } from '@vue/test-utils'
import ETimePicker from '../ETimePicker'
import { waitForDebounce } from '@/test/util'
import flushPromises from 'flush-promises'

Vue.use(Vuetify)
Vue.use(formBaseComponents)
Vue.use(dayjs)

describe('An ETimePicker', () => {
  let vuetify

  const TIME_1 = '2037-07-18T09:52:00+00:00'
  const TIME_1_HHMM = '09:52'
  const TIME_2 = '1989-10-27T18:33:00+00:00'
  const TIME_3 = '1989-10-27T19:15:00+00:00'
  const INVALID_TIME_1 = 'some time'
  const INVALID_TIME_2 = '1989-10-27T46:89:00+00:00'

  const localeData = [
    [
      'de',
      {
        time_1: '09:52',
        time_2: '18:33',
        time_3: '19:15'
      }
    ],
    [
      'en',
      {
        time_1: '9:52 AM',
        time_2: '6:33 PM',
        time_3: '7:15 PM'
      }
    ]
  ]

  const mount = (options) => mountComponent(ETimePicker, { vuetify, i18n, ...options })

  describe.each(localeData)('in locale %s', (locale, data) => {
    beforeEach(() => {
      i18n.locale = locale
      Vue.dayjs.locale(locale)
      vuetify = new Vuetify()
    })

    test('renders', async () => {
      const wrapper = mount({
        propsData: {
          value: TIME_1
        }
      })
      await flushPromises()
      expect(wrapper.find('input[type=text]').element.value).toBe(data.time_1)
    })

    test('looks like a time picker', async () => {
      const wrapper = mountComponent(
        {
          data: () => ({ time: TIME_1 }),
          template: '<div data-app><e-time-picker v-model="time"></e-time-picker></div>',
          components: { 'e-time-picker': ETimePicker }
        },
        {
          vuetify,
          attachTo: document.body,
          i18n
        }
      )
      await waitForDebounce()
      expect(wrapper).toMatchSnapshot('pickerclosed')
      await wrapper.find('button').trigger('click')
      expect(wrapper).toMatchSnapshot('pickeropen')
      wrapper.destroy()
    })

    test('allows a different valueFormat', async () => {
      const wrapper = mount({
        propsData: {
          value: TIME_1_HHMM,
          valueFormat: 'HH:mm'
        }
      })
      await flushPromises()
      expect(wrapper.find('input[type=text]').element.value).toBe(data.time_1)
    })

    test('updates v-model when the value changes', async () => {
      const wrapper = mountComponent(
        {
          data: () => ({ time: TIME_2 }),
          template: '<div><e-time-picker v-model="time"></e-time-picker></div>',
          components: { 'e-time-picker': ETimePicker }
        },
        {
          vuetify,
          i18n
        }
      )
      expect(wrapper.vm.time).toBe(TIME_2)
      const inputSpy = jest.fn()
      wrapper.findComponent(ETimePicker).vm.$on('input', (event) => inputSpy(event))
      const input = wrapper.find('input[type=text]')
      await input.setValue(data.time_3)
      await waitForDebounce()
      expect(inputSpy).toBeCalledTimes(1)
      expect(inputSpy).toBeCalledWith(TIME_3)
      expect(wrapper.vm.time).toBe(TIME_3)
    })

    test('validates the input', async () => {
      const wrapper = mount({
        propsData: {
          value: TIME_1
        }
      })
      const input = wrapper.find('input[type=text]')
      await input.setValue(INVALID_TIME_1)
      await waitForDebounce()
      expect(wrapper.text()).toContain('invalid format')
      await input.setValue(INVALID_TIME_2)
      await waitForDebounce()
      expect(wrapper.text()).toContain('invalid format')
    })

    test('works with invalid initialization', async () => {
      const wrapper = mount({
        propsData: {
          value: 'abc'
        }
      })
      await waitForDebounce()
      expect(wrapper.find('input[type=text]').element.value).toBe('Invalid Date')
      expect(wrapper.text()).toContain('invalid format')
      const input = wrapper.find('input[type=text]')
      await input.setValue(data.time_1)
      await waitForDebounce()
      expect(wrapper.text()).not.toContain('invalid format')
    })

    test('updates its value when a time is picked', async () => {
      const wrapper = mountComponent(
        {
          data: () => ({ time: TIME_2 }),
          template: '<div data-app><e-time-picker v-model="time"></e-time-picker></div>',
          components: { 'e-time-picker': ETimePicker }
        },
        {
          vuetify,
          attachTo: document.body,
          i18n
        }
      )
      await waitForDebounce()
      // open the time picker
      const openPicker = wrapper.find('button')
      await openPicker.trigger('click')
      // select hour
      const clockHour = wrapper.findComponent({ name: 'v-time-picker-clock' })
      clockHour.vm.update(19)
      clockHour.vm.valueOnMouseUp = 19
      await clockHour.trigger('mouseup')
      // select minute
      const clockMinute = wrapper.findComponent({ name: 'v-time-picker-clock' })
      clockMinute.vm.update(15)
      clockMinute.vm.valueOnMouseUp = 15
      await clockMinute.trigger('mouseup')
      // click the save button
      const closeButton = wrapper.find('[data-testid="action-ok"]')
      await closeButton.trigger('click')
      await waitForDebounce()
      expect(wrapper.find('input[type=text]').element.value).toBe(data.time_3)
      wrapper.destroy()
    })
  })
})
