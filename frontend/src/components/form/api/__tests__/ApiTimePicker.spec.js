import ApiTimePicker from '../ApiTimePicker'
import ApiWrapper from '@/components/form/api/ApiWrapper'
import Vue from 'vue'
import Vuetify from 'vuetify'
import dayjs from '@/plugins/dayjs'
import flushPromises from 'flush-promises'
import formBaseComponents from '@/plugins/formBaseComponents'
import merge from 'lodash/merge'
import { ApiMock } from '@/components/form/api/__tests__/ApiMock'
import { i18n } from '@/plugins'
import { mount as mountComponent } from '@vue/test-utils'
import { waitForDebounce } from '@/test/util'
import { HTML5_FMT } from '@/common/helpers/dateFormat.js'

Vue.use(Vuetify)
Vue.use(formBaseComponents)
Vue.use(dayjs)

describe('An ApiTimePicker', () => {
  let vuetify
  let wrapper
  let apiMock

  const fieldName = 'test-field/123'
  const TIME_1 = '2037-07-18T09:52:00+00:00'
  const TIME_2_HOUR = 19
  const TIME_2_MINUTE = 15
  const TIME_2 = `2037-07-18T${TIME_2_HOUR}:${TIME_2_MINUTE}:00+00:00`

  const format = (date) =>
    Vue.dayjs.utc(date, HTML5_FMT.DATETIME_LOCAL_SECONDS).format('LT')

  beforeEach(() => {
    i18n.locale = 'de'
    Vue.dayjs.locale(i18n.locale)
    vuetify = new Vuetify()
    apiMock = ApiMock.create()
  })

  afterEach(() => {
    jest.restoreAllMocks()
    wrapper.destroy()
  })

  const mount = (options) => {
    const app = Vue.component('App', {
      components: { ApiTimePicker },
      props: {
        fieldName: { type: String, default: fieldName }
      },
      template: `
        <div data-app>
          <api-time-picker
            :auto-save="false"
            :fieldname="fieldName"
            uri="test-field/123"
            label="Test field"
            required="true"
          />
        </div>
      `
    })
    apiMock.get().thenReturn(ApiMock.success(TIME_1).forFieldName(fieldName))
    const defaultOptions = {
      mocks: {
        $tc: () => {},
        api: apiMock.getMocks()
      }
    }
    return mountComponent(app, {
      vuetify,
      i18n,
      attachTo: document.body,
      ...merge(defaultOptions, options)
    })
  }

  test('triggers api.patch and status update if input changes', async () => {
    apiMock.patch().thenReturn(ApiMock.success(TIME_2))
    wrapper = mount()

    await flushPromises()

    // open the time picker
    const openPicker = wrapper.find('button')
    await openPicker.trigger('click')
    // select hour
    const clockHour = wrapper.findComponent({ name: 'v-time-picker-clock' })
    clockHour.vm.update(TIME_2_HOUR)
    clockHour.vm.valueOnMouseUp = TIME_2_HOUR
    await clockHour.trigger('mouseup')
    // select minute
    const clockMinute = wrapper.findComponent({ name: 'v-time-picker-clock' })
    clockMinute.vm.update(TIME_2_MINUTE)
    clockMinute.vm.valueOnMouseUp = TIME_2_MINUTE
    await clockMinute.trigger('mouseup')
    // click the save button
    const closeButton = wrapper.find('[data-testid="action-ok"]')
    await closeButton.trigger('click')
    await wrapper.find('input').trigger('submit')
    await waitForDebounce()

    await waitForDebounce()
    await flushPromises()

    expect(apiMock.getMocks().patch).toBeCalledTimes(1)
    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(TIME_2)
  })

  test('updates state if value in store is refreshed and has new value', async () => {
    wrapper = mount()
    apiMock.get().thenReturn(ApiMock.success(TIME_2).forFieldName(fieldName))

    wrapper.findComponent(ApiWrapper).vm.reload()

    await waitForDebounce()
    await flushPromises()

    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(TIME_2)
    expect(wrapper.find('input[type=text]').element.value).toBe(format(TIME_2))
  })
})
