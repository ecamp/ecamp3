// Libraries
import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'

import { mount as mountComponent } from '@vue/test-utils'
import ApiSelect from '../ApiSelect.vue'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('An ApiSelect', () => {
  let vuetify

  beforeEach(() => {
    vuetify = new Vuetify()
  })

  const mount = (options) => {
    const app = Vue.component('App', {
      components: { ApiSelect },
      data: function () {
        return {
          data: 'Test Value'
        }
      },
      template: `
          <div data-app>
            <api-select
              v-model="data"
              fieldname="test-field/123"
              uri="test-field/123"
              label="Test field"
              required="true"
            />
          </div>
        `
    })
    return mountComponent(app, { vuetify, attachTo: document.body, ...options })
  }

  test('renders correctly', async () => {
    const wrapper = mount()
    expect(wrapper).toMatchSnapshot()
  })
})
