import Vue from 'vue'
import VueI18n from 'vue-i18n'
import deepmerge from 'deepmerge'

import en from '@/locales/en.json'
import de from '@/locales/de.json'
import deScout from '@/locales/de-scout.json'

import validationEn from 'vee-validate/dist/locale/en.json'
import validationDe from 'vee-validate/dist/locale/de.json'

Vue.use(VueI18n)

export default new VueI18n({
  locale: 'de',
  fallbackLocale: 'en',
  messages: deepmerge({ en, de, 'de-scout': deScout }, {
    de: {
      validation: validationDe.messages
    },
    en: {
      validation: validationEn.messages
    }
  }),
  silentTranslationWarn: true,
  availableLocales: ['de', 'de-scout', 'en', 'fr', 'it']
})
