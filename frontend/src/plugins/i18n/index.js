import { createI18n } from 'vue-i18n'
import deepmerge from 'deepmerge'

import itCommon from '@/common/locales/it.json'
import itCHScoutCommon from '@/common/locales/it-CH-scout.json'
import frCommon from '@/common/locales/fr.json'
import frCHScoutCommon from '@/common/locales/fr-CH-scout.json'
import enCommon from '@/common/locales/en.json'
import enCHScoutCommon from '@/common/locales/en-CH-scout.json'
import deCommon from '@/common/locales/de.json'
import deCHScoutCommon from '@/common/locales/de-CH-scout.json'
import rmCommon from '@/common/locales/rm.json'
import rmCHScoutCommon from '@/common/locales/rm-CH-scout.json'

import it from '@/locales/it.json'
import itCHScout from '@/locales/it-CH-scout.json'
import fr from '@/locales/fr.json'
import frCHScout from '@/locales/fr-CH-scout.json'
import en from '@/locales/en.json'
import enCHScout from '@/locales/en-CH-scout.json'
import de from '@/locales/de.json'
import deCHScout from '@/locales/de-CH-scout.json'
import rm from '@/locales/rm.json'
import rmCHScout from '@/locales/rm-CH-scout.json'

import validationIt from '@vee-validate/i18n/dist/locale/it.json'
import validationFr from '@vee-validate/i18n/dist/locale/fr.json'
import validationEn from '@vee-validate/i18n/dist/locale/en.json'
import validationDe from '@vee-validate/i18n/dist/locale/de.json'

import vuetifyEn from 'vuetify/lib/locale/en'
import vuetifyDe from 'vuetify/lib/locale/de'
import vuetifyFr from 'vuetify/lib/locale/fr'
import vuetifyIt from 'vuetify/lib/locale/it'

const fallbackLocales = {
  rm: ['de'],
  default: 'en',
}

const i18n = createI18n({
  locale: 'de',
  fallbackLocale: fallbackLocales,
  messages: deepmerge.all([
    // vee-validate locales
    {
      it: {
        global: {
          validation: validationIt.messages,
        },
      },
      fr: {
        global: {
          validation: validationFr.messages,
        },
      },
      en: {
        global: {
          validation: validationEn.messages,
        },
      },
      de: {
        global: {
          validation: validationDe.messages,
        },
      },
    },

    // vuetify locales
    {
      en: { $vuetify: vuetifyEn },
      de: { $vuetify: vuetifyDe },
      fr: { $vuetify: vuetifyFr },
      it: { $vuetify: vuetifyIt },
    },

    // eCamp common locales
    {
      it: itCommon,
      'it-CH-scout': itCHScoutCommon,
      fr: frCommon,
      'fr-CH-scout': frCHScoutCommon,
      en: enCommon,
      'en-CH-scout': enCHScoutCommon,
      de: deCommon,
      'de-CH-scout': deCHScoutCommon,
      rm: rmCommon,
      'rm-CH-scout': rmCHScoutCommon,
    },

    // eCamp frontend only locales
    {
      it,
      'it-CH-scout': itCHScout,
      fr,
      'fr-CH-scout': frCHScout,
      en,
      'en-CH-scout': enCHScout,
      de,
      'de-CH-scout': deCHScout,
      rm,
      'rm-CH-scout': rmCHScout,
    },
  ]),
  silentTranslationWarn: true,
})

// Vue.use(i18n)

Object.defineProperty(i18n, 'browserPreferredLocale', {
  get: function () {
    const languages = navigator.languages || [navigator.language]
    for (const language of languages) {
      if (this.availableLocales.includes(language)) {
        return language
      }

      const languageFallback = language.substring(0, 2)
      if (this.availableLocales.includes(languageFallback)) {
        return languageFallback
      }
    }
    return fallbackLocales.default
  },
})

export default i18n

export { i18n, fallbackLocales }
