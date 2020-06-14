import { extend, configure } from 'vee-validate'
import * as rules from 'vee-validate/dist/rules'
import i18n from '@/plugins/i18n'

class VeeValidatePlugin {
  install (Vue, options) {
    // translate default error messages
    configure({
    // this will be used to generate messages.
      defaultMessage: (field, values) => {
        return i18n.t(`validation.${values._rule_}`, values)
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
        return new Date(value) >= new Date(min)
      },
      message: (field, values) => i18n.t('global.validation.minDate', values)
    })
  }
}

export default new VeeValidatePlugin()
