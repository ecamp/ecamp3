import { configure, defineRule } from 'vee-validate'
import { localize, setLocale } from '@vee-validate/i18n'
import * as rules from '@vee-validate/rules'

// import greaterThan_time from './veeValidate/greaterThan_time.js'
// import greaterThanOrEqual_date from './veeValidate/greaterThanOrEqual_date.js'
// import lessThanOrEqual_date from './veeValidate/lessThanOrEqual_date.js'

import it from '@vee-validate/i18n/dist/locale/it.json'
import fr from '@vee-validate/i18n/dist/locale/fr.json'
import en from '@vee-validate/i18n/dist/locale/en.json'
import de from '@vee-validate/i18n/dist/locale/de.json'

class VeeValidatePlugin {
  install() {
    // Eager = Lazy at the beginning, Agressive once the field is invalid (https://vee-validate.logaretm.com/v3/guide/interaction-and-ux.html#interaction-modes)
    // setInteractionMode('eager')

    configure({
      // TODO: this is using localize from vee-validate instead of vue-i18n.
      // vee-validate messages are not directly compatible with vue-i18n, so some message format change would be needed,
      // if we want to use vue-i18n here (see alo https://github.com/logaretm/vee-validate/issues/3684).
      // Not using vue-18n will break translation for our own custom validators below, so we still need to fix this.
      generateMessage: localize({ en, de, fr, it }),
    })

    setLocale('en')

    // install all default rules
    Object.keys(rules).forEach((rule) => {
      defineRule(rule, rules[rule])
    })

    /**
     * define custom rules
     */

    // extend('greaterThan_time', greaterThan_time(Vue.dayjs, i18n))
    //
    // // check if date (value) is equal or larger than another date (min)
    // extend('greaterThanOrEqual_date', greaterThanOrEqual_date(Vue.dayjs, i18n))
    //
    // // check if date (value) is equal or less than another date (max)
    // extend('lessThanOrEqual_date', lessThanOrEqual_date(Vue.dayjs, i18n))
  }
}

export default new VeeValidatePlugin()
