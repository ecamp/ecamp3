// Libraries
import Vue from 'vue'
import Vuetify from 'vuetify'

import { mount, createLocalVue } from '@vue/test-utils'
import ApiSingleSelect from '../ApiSingleSelect.vue'

// const localVue = createLocalVue()
// localVue.use(Vuetify)

Vue.use(Vuetify)

describe('ApiTextField.vue', () => {
  let vuetify

  beforeEach(() => {
    vuetify = new Vuetify()
  })

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

      /* enable/disable edit mode */
      editing: true,

      /* enable/disable auto save */
      autoSave: true,

      /* Validation criteria */
      required: true
    }

    const wrapper = mount(ApiSingleSelect, {
      // localVue,
      vuetify,
      propsData: props
    })

    expect(wrapper.element).toMatchSnapshot()
  })
})
