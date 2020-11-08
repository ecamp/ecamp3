import Vue from 'vue'
import VueI18n from 'vue-i18n'
import deepmerge from 'deepmerge'

import availableLocales from './availableLocales'

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

Vue.use(VueI18n)

const i18n = new VueI18n({
  locale: 'de',
  fallbackLocale: 'en',
  messages: deepmerge({
    it: {
      global: {
        validation: validationIt.messages
      }
    },
    fr: {
      global: {
        validation: validationFr.messages
      }
    },
    en: {
      global: {
        validation: validationEn.messages
      }
    },
    de: {
      global: {
        validation: validationDe.messages
      }
    }
  }, { it, 'it-CH-scout': itCHScout, fr, 'fr-CH-scout': frCHScout, en, 'en-CH-scout': enCHScout, de, 'de-CH-scout': deCHScout }),
  silentTranslationWarn: true,
  availableLocales
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
  }
})

export default i18n
