import ApiDatePicker from '../ApiDatePicker'
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

describe('An ApiDatePicker', () => {
  let vuetify
  let wrapper
  let apiMock

  const fieldName = 'test-field/123'
  const DATE_1 = '2020-03-01'
  const DATE_2 = '2020-03-24'

  const format = (date) => Vue.dayjs.utc(date, HTML5_FMT.DATE).format('DD.MM.YYYY')

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
      components: { ApiDatePicker },
      props: {
        fieldName: { type: String, default: fieldName },
      },
      template: `
        <div data-app>
          <api-date-picker
            :auto-save="false"
            :fieldname="fieldName"
            uri="test-field/123"
            label="Test field"
            required="true"
          />
        </div>
      `,
    })
    apiMock.get().thenReturn(ApiMock.success(DATE_1).forFieldName(fieldName))
    const defaultOptions = {
      mocks: {
        $tc: () => {},
        api: apiMock.getMocks(),
      },
    }
    return mountComponent(app, {
      vuetify,
      i18n,
      attachTo: document.body,
      ...merge(defaultOptions, options),
    })
  }

  test('triggers api.patch and status update if input changes', async () => {
    apiMock.patch().thenReturn(ApiMock.success(DATE_2))
    wrapper = mount()

    await flushPromises()

    // open the date picker
    const openPicker = wrapper.find('button')
    await openPicker.trigger('click')
    // click on day 24 of the month
    await wrapper
      .findAll('button')
      .filter((node) => node.text() === '24')
      .at(0)
      .trigger('click')
    // click the save button
    const closeButton = wrapper.find('[data-testid="action-ok"]')
    await closeButton.trigger('click')
    await wrapper.find('input').trigger('submit')

    await waitForDebounce()
    await flushPromises()

    expect(apiMock.getMocks().patch).toBeCalledTimes(1)
    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(DATE_2)
  })

  test('updates state if value in store is refreshed and has new value', async () => {
    wrapper = mount()
    apiMock.get().thenReturn(ApiMock.success(DATE_2).forFieldName(fieldName))

    wrapper.findComponent(ApiWrapper).vm.reload()

    await waitForDebounce()
    await flushPromises()

    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(DATE_2)
    expect(wrapper.find('input[type=text]').element.value).toBe(format(DATE_2))
  })
})
