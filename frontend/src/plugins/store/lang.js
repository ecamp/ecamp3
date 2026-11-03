import axios from 'axios'
import VueI18n from '@/plugins/i18n'
import { setLocale as veeValidateSetLocale } from '@vee-validate/i18n'
import dayjs from '@/helpers/dayjs.js'
import { toDayjsLocale } from '@/helpers/dayjs.js'

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
    VueI18n.global.locale.value = lang
    dayjs.locale(toDayjsLocale(lang))
    veeValidateSetLocale(lang.substring(0, 2))
    axios.defaults.headers.common['Accept-Language'] = lang
    document.querySelector('html').setAttribute('lang', lang)
    window.localStorage.setItem(LANG_KEY, lang)
  },
}

export default {
  state,
  mutations,
}
