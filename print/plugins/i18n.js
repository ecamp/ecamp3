import Vue from 'vue'
import VueI18n from 'vue-i18n'

import deepmerge from 'deepmerge'

import availableLocales from '~/../common/locales/availableLocales'
import enCommon from '~/../common/locales/en.json'
import deCommon from '~/../common/locales/de.json'

import de from '@/locales/de.json'
import en from '@/locales/en.json'

Vue.use(VueI18n)

export default ({ app, store }) => {
  app.i18n = new VueI18n({
    locale: 'de',
    fallbackLocale: 'en',
    messages: deepmerge(
      {
        en: enCommon,
        de: deCommon,
      },
      {
        en,
        de,
      }
    ),
    silentTranslationWarn: true,
    availableLocales,
  })
}
