import Vue from 'vue'
import VueI18n from 'vue-i18n'

import en from '@/locales/en.json'
import de from '@/locales/de.json'

Vue.use(VueI18n)

function loadLocaleMessages () {
  return { en, de }
}

export default new VueI18n({
  locale: 'de',
  fallbackLocale: 'en',
  messages: loadLocaleMessages(),
  silentTranslationWarn: true,
  availableLocales: ['de', 'en', 'fr', 'it']
})
