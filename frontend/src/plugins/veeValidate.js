import { configure, defineRule } from 'vee-validate'
import { localize } from '@vee-validate/i18n'
import { all } from '@vee-validate/rules'
import i18n from '@/plugins/i18n'
import dayjs from '@/helpers/dayjs.js'

import greaterThan from '@/plugins/veeValidate/greaterThan'
import greaterThan_dateTime from '@/plugins/veeValidate/greaterThan_dateTime.js'
import greaterThan_time from './veeValidate/greaterThan_time.js'
import greaterThanOrEqual_date from './veeValidate/greaterThanOrEqual_date.js'
import lessThanOrEqual_date from './veeValidate/lessThanOrEqual_date.js'
import oneEmojiOrTwoCharacters from '@/plugins/veeValidate/oneEmojiOrTwoCharacters.js'

import it from '@vee-validate/i18n/dist/locale/it.json'
import fr from '@vee-validate/i18n/dist/locale/fr.json'
import en from '@vee-validate/i18n/dist/locale/en.json'
import de from '@vee-validate/i18n/dist/locale/de.json'

class VeeValidatePlugin {
  install() {
    // Eager = Lazy at the beginning, Agressive once the field is invalid (https://vee-validate.logaretm.com/v3/guide/interaction-and-ux.html#interaction-modes)
    // setInteractionMode('eager')

    // translate default error messages
    configure({
      // TODO: this is using localize from vee-validate instead of vue-i18n.
      // vee-validate messages are not directly compatible with vue-i18n, so some message format change would be needed,
      // if we want to use vue-i18n here (see alo https://github.com/logaretm/vee-validate/issues/3684).
      // Not using vue-18n will break translation for our own custom validators below, so we still need to fix this.
      generateMessage: localize({ en, de, fr, it }),
    })

    // install all default rules
    Object.entries(all).forEach(([name, rule]) => {
      defineRule(name, rule)
    })

    /**
     * define custom rules
     */
    defineRule('greaterThan', greaterThan(i18n))

    defineRule('greaterThan_time', greaterThan_time(dayjs, i18n))

    defineRule('greaterThan_dateTime', greaterThan_dateTime(dayjs, i18n))

    // check if date (value) is equal or larger than another date (min)
    defineRule('greaterThanOrEqual_date', greaterThanOrEqual_date(dayjs, i18n))

    // check if date (value) is equal or less than another date (max)
    defineRule('lessThanOrEqual_date', lessThanOrEqual_date(dayjs, i18n))

    // check if date (value) is equal or less than another date (max)
    defineRule('oneEmojiOrTwoCharacters', oneEmojiOrTwoCharacters(i18n))
  }
}

export default new VeeValidatePlugin()
