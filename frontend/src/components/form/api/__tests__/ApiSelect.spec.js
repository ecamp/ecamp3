// Libraries
import Vue from 'vue'
import Vuetify from 'vuetify'

import formBaseComponents from '@/plugins/formBaseComponents'

import { mount } from '@vue/test-utils'
import ApiSelect from '../ApiSelect.vue'

Vue.use(Vuetify)
Vue.use(formBaseComponents)

describe('ApiTextField.vue', () => {
  let vuetify

  beforeEach(() => {
    vuetify = new Vuetify()
  })

  // keep this the first test --> otherwise element IDs change constantly
  test('renders correctly', () => {
    const props = {
      value: 'Test Value',

      /* field name and URI for saving back to API */
      fieldname: 'test-field',
      uri: 'test-field/123',

      /* display label */
      label: 'Test Field',

      /* overrideDirty=true will reset the input if 'value' changes, even if the input is dirty. Use with caution. */
      overrideDirty: false,

      /* enable/disable auto save */
      autoSave: true,

      /* Validation criteria */
      required: true
    }

    const wrapper = mount(ApiSelect, {
      vuetify,
      propsData: props
    })

    expect(wrapper.element).toMatchSnapshot()
  })
})
