import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios/dist/vue-axios.common.min'
import { HalJsonVuexPlugin } from 'hal-json-vuex'
import lang from './lang'
import auth from './auth'
import preferences from './preferences'
import { getEnv } from '@/environment.js'

class StorePlugin {
  install(Vue) {
    Vue.use(Vuex)

    store = new Vuex.Store({
      modules: {
        lang,
        auth,
        preferences,
      },
      strict: process.env.NODE_ENV !== 'production',
    })

    axios.defaults.withCredentials = true
    axios.defaults.baseURL = getEnv().API_ROOT_URL
    axios.defaults.headers.common.Accept = 'application/hal+json'
    axios.interceptors.request.use(function (config) {
      if (config.method === 'patch') {
        config.headers['Content-Type'] = 'application/merge-patch+json'
      }
      return config
    })

    Vue.use(VueAxios, axios)

    /**
     * @type apiStore {import('hal-json-vuex').HalJsonVuex<RootEndpoint>}
     */
    apiStore = HalJsonVuexPlugin(store, axios, { forceRequestedSelfLink: true })
    Vue.use(apiStore)
  }
}

export let apiStore
export let store

export default new StorePlugin()
