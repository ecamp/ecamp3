import Vue from 'vue'
import Vuetify from 'vuetify'
import { regex } from 'vee-validate/dist/rules'
import { extend } from 'vee-validate'

import i18n from '@/plugins/i18n'
import formBaseComponents from '@/plugins/formBaseComponents'

import { mount as mountComponent } from '@vue/test-utils'
import EColorPicker from '../EColorPicker'
import { waitForDebounce } from '@/test/util'
import flushPromises from 'flush-promises'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

extend('regex', regex)

describe('An EColorPicker', () => {
  let vuetify

  const COLOR_1 = '#ff0000'
  const COLOR_2 = '#ff00ff'
  const INVALID_COLOR = 'some new color'

  const createMouseEvent = (x, y) => {
    return {
      preventDefault: () => {},
      clientX: x,
      clientY: y,
    }
  }

  const rectMock = {
    bottom: 0,
    height: 100,
    width: 100,
    left: 0,
    right: 0,
    top: 0,
    x: 0,
    y: 0,
  }

  const mount = (options) => mountComponent(EColorPicker, { vuetify, i18n, ...options })

  beforeEach(() => {
    vuetify = new Vuetify()
  })

  test('renders', async () => {
    const wrapper = mount({
      propsData: {
        value: COLOR_1,
      },
    })
    await flushPromises()
    expect(wrapper.find('input[type=text]').element.value).toBe(COLOR_1)
  })

  test('looks like a color picker', async () => {
    const wrapper = mountComponent(
      {
        data: () => ({ color: COLOR_1 }),
        template: '<div data-app><e-color-picker v-model="color"></e-color-picker></div>',
        components: { 'e-color-picker': EColorPicker },
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
        data: () => ({ color: COLOR_1 }),
        template: '<div><e-color-picker v-model="color"></e-color-picker></div>',
        components: { 'e-color-picker': EColorPicker },
      },
      {
        vuetify,
        i18n,
      }
    )
    expect(wrapper.vm.color).toBe(COLOR_1)
    const inputSpy = jest.fn()
    wrapper.findComponent(EColorPicker).vm.$on('input', (event) => inputSpy(event))
    const input = wrapper.find('input[type=text]')
    await input.setValue(COLOR_2)
    await waitForDebounce()
    expect(inputSpy).toBeCalledTimes(1)
    expect(inputSpy).toBeCalledWith(COLOR_2)
    expect(wrapper.vm.color).toBe(COLOR_2)
  })

  test('validates the input', async () => {
    const wrapper = mount({
      propsData: {
        value: COLOR_1,
        name: 'Color',
      },
    })
    const input = wrapper.find('input[type=text]')
    await input.setValue(INVALID_COLOR)
    await waitForDebounce()
    expect(wrapper.text()).toContain('Color is not valid.')
  })

  test('updates its value when a color is picked', async () => {
    const wrapper = mountComponent(
      {
        data: () => ({ color: COLOR_2 }),
        template: '<div data-app><e-color-picker v-model="color"></e-color-picker></div>',
        components: { 'e-color-picker': EColorPicker },
      },
      {
        vuetify,
        attachTo: document.body,
        i18n,
      }
    )
    await waitForDebounce()
    // open the color picker
    const openPicker = wrapper.find('button')
    await openPicker.trigger('click')
    // click inside the color picker canvas to select a different color
    const canvas = wrapper.findComponent({ name: 'v-color-picker-canvas' })
    canvas.vm.$el.getBoundingClientRect = () => rectMock
    canvas.vm.handleClick(createMouseEvent(10, 10))
    await flushPromises()
    await waitForDebounce()
    expect(wrapper.find('input[type=text]').element.value).toBe('#E6CFE6')
    wrapper.destroy()
  })

  test('accepts 3-digit hex color codes', async () => {
    const wrapper = mountComponent(
      {
        data: () => ({ color: '#abc' }),
        template: '<div data-app><e-color-picker v-model="color"></e-color-picker></div>',
        components: { 'e-color-picker': EColorPicker },
      },
      {
        vuetify,
        attachTo: document.body,
        i18n,
      }
    )
    await waitForDebounce()
    // open the color picker
    const openPicker = wrapper.find('button')
    await openPicker.trigger('click')
    await waitForDebounce()
    expect(wrapper.find('input[type=text]').element.value).toBe('#AABBCC')
    wrapper.destroy()
  })
})
