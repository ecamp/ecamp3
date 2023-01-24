import { extend, configure, setInteractionMode } from 'vee-validate'
import * as rules from 'vee-validate/dist/rules'
import i18n from '@/plugins/i18n'
import greaterThan_time from './veeValidate/greaterThan_time.js'
import greaterThanOrEqual_date from './veeValidate/greaterThanOrEqual_date.js'

class VeeValidatePlugin {
  install(Vue) {
    // Eager = Lazy at the beginning, Agressive once the field is invalid (https://vee-validate.logaretm.com/v3/guide/interaction-and-ux.html#interaction-modes)
    setInteractionMode('eager')

    // translate default error messages
    configure({
      // this will be used to generate messages.
      defaultMessage: (field, values) => {
        return i18n.tc(`global.validation.${values._rule_}`, 0, values)
      },
    })

    // install all default rules
    Object.keys(rules).forEach((rule) => {
      extend(rule, {
        ...rules[rule], // copies rule configuration
      })
    })

    /**
     * define custom rules
     */

    // check if date (value) is equal or larger than another date (min)
    extend('greaterThanOrEqual_date', greaterThanOrEqual_date(Vue.dayjs, i18n))
    extend('greaterThan_time', greaterThan_time(Vue.dayjs, i18n))
  }
}

export default new VeeValidatePlugin()
