import Vue from 'vue'
import VueI18n from 'vue-i18n'
import deepmerge from 'deepmerge'

import availableLocales from './availableLocales'

import en from '@/locales/en.json'
import de from '@/locales/de.json'
import fr from '@/locales/fr.json'
import it from '@/locales/it.json'
import enCHScout from '@/locales/en-CH-scout.json'
import deCHScout from '@/locales/de-CH-scout.json'
import frCHScout from '@/locales/fr-CH-scout.json'
import itCHScout from '@/locales/it-CH-scout.json'

import validationEn from 'vee-validate/dist/locale/en.json'
import validationDe from 'vee-validate/dist/locale/de.json'
import validationFr from 'vee-validate/dist/locale/fr.json'
import validationIt from 'vee-validate/dist/locale/it.json'

Vue.use(VueI18n)

export default new VueI18n({
  locale: 'de',
  fallbackLocale: 'en',
  messages: deepmerge({
    de: {
      global: {
        validation: validationDe.messages
      }
    },
    en: {
      global: {
        validation: validationEn.messages
      }
    },
    fr: {
      global: {
        validation: validationFr.messages
      }
    },
    it: {
      global: {
        validation: validationIt.messages
      }
    }
  }, {
    en,
    de,
    fr,
    it,
    'en-CH-scout': enCHScout,
    'de-CH-scout': deCHScout,
    'fr-CH-scout': frCHScout,
    'it-CH-scout': itCHScout
  }),
  silentTranslationWarn: true,
  availableLocales
})
