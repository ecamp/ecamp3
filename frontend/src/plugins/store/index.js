import { createStore } from 'vuex'
import axios from 'axios'
import HalJsonVuex from 'hal-json-vuex'
import lang from './lang'
import auth from './auth'
import preferences from './preferences'
import snackbarMessagesStore from './snackbarMessagesStore'
import { getEnv } from '@/environment.js'
import { setupAxiosAuthInterceptor } from '@/plugins/auth.js'

export default {
  install: (app, _) => {
    store = createStore({
      modules: {
        lang,
        auth,
        preferences,
        snackbarMessagesStore,
      },
      strict: false,
    })

    app.use(store)

    axiosInstance = axios.create({
      withCredentials: true,
      baseURL: getEnv().API_ROOT_URL,
      headers: { common: { Accept: 'application/hal+json' } },
    })
    axiosInstance.interceptors.request.use(function (config) {
      if (config.method === 'patch') {
        config.headers['Content-Type'] = 'application/merge-patch+json'
      }
      return config
    })
    setupAxiosAuthInterceptor(axiosInstance)

    // create and inject API
    apiStore = new HalJsonVuex(store, axiosInstance, {
      forceRequestedSelfLink: true,
    })

    app.use(apiStore)
  },
}

export let apiStore
export let store
export let axiosInstance
