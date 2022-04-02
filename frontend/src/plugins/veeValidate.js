import { extend, configure } from 'vee-validate'
import * as rules from 'vee-validate/dist/rules'
import i18n from '@/plugins/i18n'

class VeeValidatePlugin {
  install (Vue, options) {
    // translate default error messages
    configure({
      // this will be used to generate messages.
      defaultMessage: (field, values) => {
        return i18n.t(`global.validation.${values._rule_}`, values)
      }
    })

    // install all default rules
    Object.keys(rules).forEach(rule => {
      extend(rule, {
        ...rules[rule] // copies rule configuration
      })
    })

    /**
     * define custom rules
     */

    // check if date (value) is equal or larger than another date (min)
    extend('minDate', {
      params: ['min'],
      validate: (value, { min }) => {
        const minDate = Vue.dayjs.utc(min, 'L')
        const valueDate = Vue.dayjs.utc(value, 'L')
        return valueDate.diff(minDate, 'day') >= 0
      },
      message: (field, values) => i18n.t('global.validation.minDate', values)
    })
    
    extend('pwConfirmed', {
      params: ['target'],
      validate: (value, { target }) => {
        return value === target
      },
      message: (field, values) => i18n.t('global.validation.pwConfirmed', values)
    })
    
  }
}

export default new VeeValidatePlugin()
