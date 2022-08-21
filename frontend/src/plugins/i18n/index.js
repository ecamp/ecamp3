import Vue from 'vue'
import VueI18n from 'vue-i18n'
import deepmerge from 'deepmerge'

import itCommon from '@/common/locales/it.json'
import itCHScoutCommon from '@/common/locales/it-CH-scout.json'
import frCommon from '@/common/locales/fr.json'
import frCHScoutCommon from '@/common/locales/fr-CH-scout.json'
import enCommon from '@/common/locales/en.json'
import enCHScoutCommon from '@/common/locales/en-CH-scout.json'
import deCommon from '@/common/locales/de.json'
import deCHScoutCommon from '@/common/locales/de-CH-scout.json'

import it from '@/locales/it.json'
import itCHScout from '@/locales/it-CH-scout.json'
import fr from '@/locales/fr.json'
import frCHScout from '@/locales/fr-CH-scout.json'
import en from '@/locales/en.json'
import enCHScout from '@/locales/en-CH-scout.json'
import de from '@/locales/de.json'
import deCHScout from '@/locales/de-CH-scout.json'

import validationIt from 'vee-validate/dist/locale/it.json'
import validationFr from 'vee-validate/dist/locale/fr.json'
import validationEn from 'vee-validate/dist/locale/en.json'
import validationDe from 'vee-validate/dist/locale/de.json'

import vuetifyEn from 'vuetify/lib/locale/en'
import vuetifyDe from 'vuetify/lib/locale/de'
import vuetifyFr from 'vuetify/lib/locale/fr'
import vuetifyIt from 'vuetify/lib/locale/it'

Vue.use(VueI18n)

const i18n = new VueI18n({
  locale: 'de',
  fallbackLocale: 'en',
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
    },
  ]),
  silentTranslationWarn: true,
})

Object.defineProperty(i18n, 'browserPreferredLocale', {
  get: function () {
    const languages = navigator.languages || [navigator.language]
    for (const language of languages) {
      if (this.availableLocales.includes(language)) {
        return language
      }
    }
    return undefined
  },
})

export default i18n

export { i18n }
