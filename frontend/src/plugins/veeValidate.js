import { extend, configure } from 'vee-validate'
import * as rules from 'vee-validate/dist/rules'
import i18n from '@/plugins/i18n'

class VeeValidatePlugin {
  install(Vue) {
    // translate default error messages
    configure({
      // this will be used to generate messages.
      defaultMessage: (field, values) => {
        return i18n.t(`global.validation.${values._rule_}`, values)
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
    extend('greaterThanOrEqual_date', {
      params: ['min'],
      /**
       * @param   {string}  value Dater value in local string format
       * @param   {string}  min   comparison valye in local string format
       * @returns {bool}          validation result
       */
      validate: (value, { min }) => {
        const minDate = Vue.dayjs.utc(min, 'L')
        const valueDate = Vue.dayjs.utc(value, 'L')
        return valueDate.diff(minDate, 'day') >= 0
      },
      message: (field, values) =>
        i18n.t('global.validation.greaterThanOrEqual_date', values),
    })

    extend('greaterThan_time', {
      params: ['min'],
      /**
       *
       * @param {string} value Time value in string format 'HH:mm'
       * @param {string} min   Comparison value in string format 'HH:mm'
       * @returns {bool}       validation result
       */
      validate: (value, { min }) => {
        const minDate = new Date('1970-01-01 ' + min)
        const valueDate = new Date('1970-01-01 ' + value)
        return valueDate > minDate
      },
      message: (field, values) => i18n.t('global.validation.greaterThan_time', values),
    })
  }
}

export default new VeeValidatePlugin()
