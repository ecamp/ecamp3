// Global registration of all Vue components in folder components/form/base

import ECheckbox from '@/components/form/base/ECheckbox.vue'
import EColorPicker from '@/components/form/base/EColorPicker.vue'
import EDatePicker from '@/components/form/base/EDatePicker.vue'
import ERichtext from '@/components/form/base/ERichtext.vue'
import ESelect from '@/components/form/base/ESelect.vue'
import ESwitch from '@/components/form/base/ESwitch.vue'
import ETextarea from '@/components/form/base/ETextarea.vue'
import ETextField from '@/components/form/base/ETextField.vue'
import ETimePicker from '@/components/form/base/ETimePicker.vue'

class FormBaseComponentsPlugin {
  install(Vue) {
    Vue.component('ECheckbox', ECheckbox)
    Vue.component('EColorPicker', EColorPicker)
    Vue.component('EDatePicker', EDatePicker)
    Vue.component('ERichtext', ERichtext)
    Vue.component('ESelect', ESelect)
    Vue.component('ESwitch', ESwitch)
    Vue.component('ETextarea', ETextarea)
    Vue.component('ETextField', ETextField)
    Vue.component('ETimePicker', ETimePicker)
  }
}

export default new FormBaseComponentsPlugin()
