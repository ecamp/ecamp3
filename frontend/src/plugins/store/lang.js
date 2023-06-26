import Vue from 'vue'
import axios from 'axios'
import VueI18n from '@/plugins/i18n'
import { localeChanged } from 'vee-validate'

const LANG_KEY = 'language'

export const state = {
  language: window.localStorage.getItem(LANG_KEY) || window.navigator.language || 'en',
}

export const mutations = {
  /**
   * Changes language
   * @param state Vuex state
   * @param lang Language string
   */
  setLanguage(state, lang) {
    if (!lang) {
      return
    }

    state.language = lang
    VueI18n.locale = lang
    const dayjsLocaleMap = {
      de: 'de-ch',
      en: 'en-gb',
      it: 'it-ch',
      fr: 'fr-ch',
    }
    Vue.dayjs.locale(
      Object.keys(dayjsLocaleMap).includes(lang) ? dayjsLocaleMap[lang] : lang
    )
    localeChanged()
    axios.defaults.headers.common['Accept-Language'] = lang
    document.querySelector('html').setAttribute('lang', lang)
    window.localStorage.setItem(LANG_KEY, lang)
  },
}

export default {
  state,
  mutations,
}
