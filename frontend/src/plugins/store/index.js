import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios/dist/vue-axios.common.min'
import HalJsonVuex from 'hal-json-vuex'
import lang from './lang'
import auth from './auth'

class StorePlugin {
  install(Vue) {
    Vue.use(Vuex)

    store = new Vuex.Store({
      modules: {
        lang,
        auth,
      },
      strict: process.env.NODE_ENV !== 'production',
    })

    axios.defaults.withCredentials = true
    axios.defaults.baseURL = window.environment.API_ROOT_URL
    axios.defaults.headers.common.Accept = 'application/hal+json'
    axios.interceptors.request.use(function (config) {
      if (config.method === 'patch') {
        config.headers['Content-Type'] = 'application/merge-patch+json'
      }
      return config
    })

    Vue.use(VueAxios, axios)

    let halJsonVuex = HalJsonVuex
    if (typeof halJsonVuex !== 'function') {
      halJsonVuex = HalJsonVuex.default
    }
    apiStore = halJsonVuex(store, axios, { forceRequestedSelfLink: true })
    Vue.use(apiStore)
  }
}

export let apiStore
export let store

export default new StorePlugin()
