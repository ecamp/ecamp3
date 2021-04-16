// Global registration of all Vue components in folder components/form/base

// import upperFirst from 'lodash/upperFirst'
// import camelCase from 'lodash/camelCase'

import ECheckbox from '@/components/form/base/ECheckbox.vue'
import EColorPicker from '@/components/form/base/EColorPicker.vue'
import EDatePicker from '@/components/form/base/EDatePicker.vue'
import ERichtext from '@/components/form/base/ERichtext.vue'
import ESelect from '@/components/form/base/ESelect.vue'
import ESwitch from '@/components/form/base/ESwitch.vue'
import ETextarea from '@/components/form/base/ETextarea.vue'
import ETextField from '@/components/form/base/ETextField.vue'
import ETimePicker from '@/components/form/base/ETimePicker.vue'

/*
const requireComponent = require.context(
  // The relative path of the components folder
  '../components/form/base',
  // Whether or not to look in subfolders
  false,
  // The regular expression used to match base component filenames
  /[A-Z]\w+\.(vue|js)$/
) */

class FormBaseComponentsPlugin {
  install (Vue, options) {
    Vue.component('ECheckbox', ECheckbox)
    Vue.component('EColorPicker', EColorPicker)
    Vue.component('EDatePicker', EDatePicker)
    Vue.component('ERichtext', ERichtext)
    Vue.component('ESelect', ESelect)
    Vue.component('ESwitch', ESwitch)
    Vue.component('ETextarea', ETextarea)
    Vue.component('ETextField', ETextField)
    Vue.component('ETimePicker', ETimePicker)

    /*
    requireComponent.keys().forEach(fileName => {
      // Get component config
      const componentConfig = requireComponent(fileName)

      // Get PascalCase name of component
      const componentName = upperFirst(
        camelCase(
          // Gets the file name regardless of folder depth
          fileName
            .split('/')
            .pop()
            .replace(/\.\w+$/, '')
        )
      )

      // Register component globally
      Vue.component(
        componentName,
        // Look for the component options on `.default`, which will
        // exist if the component was exported with `export default`,
        // otherwise fall back to module's root.
        componentConfig.default || componentConfig
      )
    }) */
  }
}

export default new FormBaseComponentsPlugin()
