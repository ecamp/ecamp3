import Vue from 'vue'
import VueI18n from 'vue-i18n'

import en from '@/locales/en.json'
import de from '@/locales/de.json'
import deScout from '@/locales/de-scout.json'

Vue.use(VueI18n)

function loadLocaleMessages () {
  return { en, de, 'de-scout': deScout }
}

export default new VueI18n({
  locale: 'de',
  fallbackLocale: 'en',
  messages: loadLocaleMessages(),
  silentTranslationWarn: true,
  availableLocales: ['de', 'de-scout', 'en', 'fr', 'it']
})
